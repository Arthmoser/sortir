<?php

namespace App\Repository;

use App\Entity\Activity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
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


    public function findBySomeField($date, $statusToUpdate)
    {
        $statusClosed = 'Clôturée';
        $statusInProgress = 'Activité en cours';
        $statusPast = 'passée';


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
            ->addSelect('users')
        ;

        $query = $qb->getQuery();

        return $query->getResult();

    }

//    public function filterActivities(FormInterface $form, Campus $campus = null)
//    {
//
//        $form->add('campus', EntityType::class, array(
//            'required' => true,
//            'data' => $campus,
//            'class' => 'AppBundle:Campus'
//        ));
//
//        $activity = array();
//
//        if ($campus) {
//            $ActivityRepository = $this->_em->getRepository('AppBundle:Activity');
//
//            $activity = $ActivityRepository->createQueryBuilder("q")
//                ->where("q.campus = :campusid")
//                ->setParameter("campusid", $campus->getId())
//                ->getQuery()
//                ->getResult();
//        }
//
//        $form->add('activity', EntityType::class, array(
//            'required' => true,
//            'class' => 'AppBundle:Activity',
//            'choices' => $activity
//        ));
//    }
//        function onPreSubmit(FormEvent $event){
//
//            $form = $event->getForm();
//            $data = $event->getData();
//
//            $campus = $this->_em->getRepository('AppBundle:Campus')->find($data['campus']);
//            $this->addElements($form, $campus);
//        }

}
