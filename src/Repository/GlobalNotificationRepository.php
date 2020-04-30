<?php

namespace App\Repository;

use App\Entity\GlobalNotification;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method GlobalNotification|null find($id, $lockMode = null, $lockVersion = null)
 * @method GlobalNotification|null findOneBy(array $criteria, array $orderBy = null)
 * @method GlobalNotification[]    findAll()
 * @method GlobalNotification[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GlobalNotificationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GlobalNotification::class);
    }

    public function findNewerNotificationsThan($dateTimeValue): ?array
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.endDate > :val')
            ->setParameter('val', $dateTimeValue)
            ->getQuery()
            ->getArrayResult()
        ;
    }
}
