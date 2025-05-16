<?php

namespace App\Repository;

use App\Entity\Carpooling;
use App\Service\RatingService;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

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

    /* if (!empty($criteria['minPrice'])) {
        $qb->andWhere('c.price >= :minPrice')
        ->setParameter('minPrice', $criteria['minPrice']);
    }

    if (!empty($criteria['maxPrice'])) {
        $qb->andWhere('c.price <= :maxPrice')
        ->setParameter('maxPrice', $criteria['maxPrice']);
    } */

    return $qb->getQuery()->getResult();
    }


    public function findBySearchAndFilterCriteria(array $criteria, RatingService $ratingService): array
    {
        
        $qb = $this->createQueryBuilder('c')
            ->leftJoin('c.cars', 'car')
            ->addSelect('car')
            ->where('c.numberSeats > 0');

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

        if (!empty($criteria['maxPrice'])) {
            $qb->andWhere('c.price <= :maxPrice')
                ->setParameter('maxPrice', $criteria['maxPrice']);
        }

        if (!empty($criteria['ecological'])) {
        $qb->andWhere('car.energy = true');
        }

        $carpoolings = $qb->getQuery()->getResult();

        if (!empty($criteria['minRating'])) {
            $carpoolings = array_filter($carpoolings, function ($carpooling) use ($ratingService, $criteria) {
                $user = $carpooling->getUsers();
                return $user && $ratingService->getAverageRatingForDriver($user->getId()) >= $criteria['minRating'];
            });
        }

        if (!empty($criteria['maxDuration'])) {
            $carpoolings = array_filter($carpoolings, function ($carpooling) use ($criteria) {
                $duration = $carpooling->getDuration();
                if (!$duration) return true;

                if (preg_match('/(?:(\\d+)h)?\\s*(?:(\\d+)min)?/', $duration, $matches)) {
                    $hours = isset($matches[1]) ? (int)$matches[1] : 0;
                    return $hours <= $criteria['maxDuration'];
                }

                return true;
            });
        }

        return $carpoolings;
    }


    public function findSimilarRides(array $criteria): array
    {
    $qb = $this->createQueryBuilder('c')
        ->where('c.numberSeats > 0');

    if (!empty($criteria['departureAddress'])) {
        $qb->andWhere('c.departureAddress LIKE :departure')
            ->setParameter('departure', '%' . $criteria['departureAddress'] . '%');
    }

    if (!empty($criteria['arrivalAddress'])) {
        $qb->andWhere('c.arrivalAddress LIKE :arrival')
            ->setParameter('arrival', '%' . $criteria['arrivalAddress'] . '%');
    }

    // Pas de filtre sur les dates pour les trajets similaires
    return $qb->orderBy('c.departureDate', 'ASC')
            ->setMaxResults(10) // optionnel : limite d'affichage
            ->getQuery()
            ->getResult();

    return $qb->getQuery()->getResult();
    }


        public function countCarpoolingsByDay(): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            SELECT DATE(departure_date) as date, COUNT(id) as count
            FROM carpooling
            GROUP BY date
            ORDER BY date ASC
        ';

        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();

        return $resultSet->fetchAllAssociative();
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