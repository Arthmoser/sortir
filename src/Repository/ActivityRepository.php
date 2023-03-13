<?php

namespace App\Repository;

use App\Entity\Activity;
use App\Entity\Campus;
use App\Entity\User;
use App\Form\Model\FilterModel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use phpDocumentor\Reflection\Types\This;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormInterface;

/**
 * @extends ServiceEntityRepository<Activity>
 *
 * @method Activity|null find($id, $lockMode = null, $lockVersion = null)
 * @method Activity|null findOneBy(array $criteria, array $orderBy = null)
 * @method Activity[]    findAll()
 * @method Activity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ActivityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Activity::class);
    }

    public function save(Activity $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Activity $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findNonArchivedActivity($date)
    {
        $oneMonthBeforeDate = clone $date;
        $oneMonthBeforeDate->modify('-1 month');

        $qb = $this->createQueryBuilder('a');

        $qb
            ->andWhere('a.startingDateTime > :val')
            ->setParameter('val', $oneMonthBeforeDate)
            ->addOrderBy('a.startingDateTime', 'DESC')
            ->leftJoin('a.status', 'sta')
            ->leftJoin('a.location', 'loc')
            ->leftJoin('a.campus', 'cam')
            ->leftJoin('a.user', "use")
            ->leftJoin('a.users', 'users')
            ->addSelect('sta')
            ->addSelect('loc')
            ->addSelect('cam')
            ->addSelect('use')
            ->addSelect('users');

        $query = $qb->getQuery();

        return $query->getResult();

    }


    public function findBySomeField($date, $statusToUpdate = '')
    {
        $statusClosed = 'CLO';
        $statusInProgress = 'AEC';
        $statusPast = 'PAS';
        $statusArchived = 'HIS';
        $oneMonthBeforeDate = clone $date;
        $oneMonthBeforeDate->modify('-1 month');

        $qb = $this->createQueryBuilder('a');

        if ($statusToUpdate == $statusClosed) {

            $qb
                ->andWhere('a.registrationDeadLine < :val')
                ->andWhere('a.startingDateTime > :val')
                ->setParameter('val', $date);

        } elseif ($statusToUpdate == $statusInProgress){

            $qb
                ->andWhere('a.startingDateTime >= :val')
                ->andWhere('a.startingDateTime + a.duration < :val')
                ->setParameter('val', $date);
        } elseif ($statusToUpdate == $statusPast){

            $qb
                ->andWhere('a.startingDateTime <= :val')
                ->andWhere('a.startingDateTime > :val2')
                ->setParameter('val', $date)
                ->setParameter('val2', $oneMonthBeforeDate);

        } elseif ($statusToUpdate == $statusArchived){

            $qb
                ->andWhere('a.startingDateTime < :val')
                ->setParameter('val', $oneMonthBeforeDate);
        }

        $qb
            ->leftJoin('a.status', 'sta')
            ->leftJoin('a.location', 'loc')
            ->leftJoin('a.campus', 'cam')
            ->leftJoin('a.user', "use")
            ->leftJoin('a.users', 'users')
            ->addSelect('sta')
            ->addSelect('loc')
            ->addSelect('cam')
            ->addSelect('use')
            ->addSelect('users');

        $query = $qb->getQuery();

        return $query->getResult();

    }

    public function filterActivities(FilterModel $form, User $user)
    {
        $qb = $this->createQueryBuilder("a");

        if ($form->getCampus())
        {
            $qb
                ->andWhere("a.campus = :campusid")
                ->setParameter("campusid", $form->getCampus()->getId());
        }

        if ($form->getSearch())
        {
            $qb
                ->andWhere("a.name LIKE :search")
                ->setParameter("search", '%' . $form->getSearch() . '%');
        }

        if ($form->getStartingDateTime())
        {
            $qb
                ->andWhere("a.startingDateTime > :startingDateTime")
                ->setParameter("startingDateTime", $form->getStartingDateTime());
        }

        if ($form->getEndingDateTime())
        {
            $qb
                ->andWhere("a.startingDateTime < :endingDateTime")
                ->setParameter("endingDateTime", $form->getEndingDateTime());
        }

        if ($form->getIsOrganiser())
        {
            $qb
                ->andWhere("a.user = :userId")
                ->setParameter("userId", $user->getId());
        }

        if ($form->getIsRegistered())
        {
            $qb
                ->andWhere(":user MEMBER OF a.users")
                ->setParameter("user", $user);
        }

        if ($form->getIsNotRegistered())
        {
            $qb
                ->andWhere(":user NOT MEMBER OF a.users")
                ->setParameter("user", $user);
        }

        if ($form->getAvailableActivity())
        {
            $qb
                ->andWhere("sta.statusCode = :statusCode")
                ->setParameter("statusCode", 'PAS');
        }

        $qb
            ->leftJoin('a.status', 'sta')
            ->leftJoin('a.location', 'loc')
            ->leftJoin('a.campus', 'cam')
            ->leftJoin('a.user', "use")
            ->leftJoin('a.users', 'users')
            ->addSelect('sta')
            ->addSelect('loc')
            ->addSelect('cam')
            ->addSelect('use')
            ->addSelect('users');


        $query = $qb->getQuery();

        return $query->getResult();

    }
//        function onPreSubmit(FormEvent $event){
//
//            $form = $event->getForm();
//            $data = $event->getData();
//
//            $campus = $this->_em->getRepository('AppBundle:Campus')->find($data['campus']);
//            $this->addElements($form, $campus);
//        }

}
