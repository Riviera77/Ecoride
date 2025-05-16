<?php

namespace App\Repository;

use App\Entity\Credit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Credit>
 */
class CreditRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Credit::class);
    }


        public function sumCreditsByDay(): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            SELECT DATE(transaction_date) as date, SUM(balance) as total
            FROM credit
            GROUP BY date
            ORDER BY date ASC
        ';

        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();

        return $resultSet->fetchAllAssociative();
    }

        public function getTotalCredits(): int
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = 'SELECT SUM(balance) as total FROM credit';

        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();

        $result = $resultSet->fetchAssociative();

        return (int) $result['total'];
    }

    //    /**
    //     * @return Credit[] Returns an array of Credit objects
    //     */
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

    //    public function findOneBySomeField($value): ?Credit
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}