<?php

namespace App\Repository;

use App\Entity\Carpooling;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Carpooling>
 */
class CarpoolingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Carpooling::class);
    }
    /* _em => getEntityManager()*/
    public function save(Carpooling $entity, bool $flush = false): void
    {
        $this->_em->persist($entity);

        if ($flush) {
            $this->_em->flush();
        }
    }
    public function remove(Carpooling $entity, bool $flush = false): void
    {
        $this->_em->remove($entity);

        if ($flush) {
            $this->_em->flush();
        }
    }
    //    /**
    //     * @return Carpooling[] Returns an array of Carpooling objects
    //     */
    public function findBySearchCriteria(array $criteria): array
{
    // $qb = method of Doctrine to create a query builder 
    // to build request SQL dynamically
    $qb = $this->createQueryBuilder('c')
        ->where('c.numberSeats > 0'); //only carpooling with available seats

    if (!empty($criteria['departureAddress'])) {
        $qb->andWhere('c.departureAddress LIKE :departure')
        ->setParameter('departure', '%' . $criteria['departureAddress'] . '%');
    }

    if (!empty($criteria['arrivalAddress'])) {
        $qb->andWhere('c.arrivalAddress LIKE :arrival')
        ->setParameter('arrival', '%' . $criteria['arrivalAddress'] . '%');
    }

    if (!empty($criteria['departureDate'])) {
        $qb->andWhere('c.departureDate = :date')
        ->setParameter('date', $criteria['departureDate']);
    }

    if (!empty($criteria['minPrice'])) {
        $qb->andWhere('c.price >= :minPrice')
        ->setParameter('minPrice', $criteria['minPrice']);
    }

    if (!empty($criteria['maxPrice'])) {
        $qb->andWhere('c.price <= :maxPrice')
        ->setParameter('maxPrice', $criteria['maxPrice']);
    }

    return $qb->getQuery()->getResult();
}
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Carpooling
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}