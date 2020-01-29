<?php

namespace App\Repository;

use App\Entity\FreqAskedQuestion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method FreqAskedQuestion|null find($id, $lockMode = null, $lockVersion = null)
 * @method FreqAskedQuestion|null findOneBy(array $criteria, array $orderBy = null)
 * @method FreqAskedQuestion[]    findAll()
 * @method FreqAskedQuestion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FreqAskedQuestionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FreqAskedQuestion::class);
    }

    // /**
    //  * @return FreqAskedQuestion[] Returns an array of FreqAskedQuestion objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?FreqAskedQuestion
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
