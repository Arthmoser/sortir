<?php

namespace App\Repository;

use App\Entity\Activity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

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
        $statusPast = 'Passée';
        $statusArchived = 'Historisée';
        $oneMonthBeforeCurrentDate = clone $date;
        $oneMonthBeforeCurrentDate->modify('-1 month');


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
                ->setParameter('val2', $oneMonthBeforeCurrentDate);

        } elseif ($statusToUpdate == $statusArchived){

            $qb
                ->andWhere('a.startingDateTime < :val')
                ->setParameter('val', $oneMonthBeforeCurrentDate);
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
}
