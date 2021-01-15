<?php

namespace App\Repository;

use App\Entity\EmailCancelRequest;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method EmailCancelRequest|null find($id, $lockMode = null, $lockVersion = null)
 * @method EmailCancelRequest|null findOneBy(array $criteria, array $orderBy = null)
 * @method EmailCancelRequest[]    findAll()
 * @method EmailCancelRequest[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EmailCancelRequestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EmailCancelRequest::class);
    }

    // /**
    //  * @return EmailCancelRequest[] Returns an array of EmailCancelRequest objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?EmailCancelRequest
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
