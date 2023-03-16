<?php

namespace App\Repository;

use App\Entity\City;
use App\Form\Model\FilterModel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<City>
 *
 * @method City|null find($id, $lockMode = null, $lockVersion = null)
 * @method City|null findOneBy(array $criteria, array $orderBy = null)
 * @method City[]    findAll()
 * @method City[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, City::class);
    }

    public function save(City $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(City $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function filterCities(FilterModel $form)
    {
        $qb = $this->createQueryBuilder("c");

        if (is_numeric($form->getSearch()))
        {
            $qb
                ->andWhere('c.zipCode LIKE :search1')
                ->setParameter(':search1', '%' . $form->getSearch() . '%');
        }
        elseif (is_string($form->getSearch()))
        {
            $qb
                ->andWhere("c.name LIKE :search2")
                ->setParameter("search2", '%' . $form->getSearch() . '%');
        }

        $qb
            ->addOrderBy('c.name', 'ASC');

        $query = $qb->getQuery();

        return $query->getResult();
    }
}
