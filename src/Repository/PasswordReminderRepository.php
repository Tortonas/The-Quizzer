<?php

namespace App\Repository;

use App\Entity\PasswordReminder;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PasswordReminder|null find($id, $lockMode = null, $lockVersion = null)
 * @method PasswordReminder|null findOneBy(array $criteria, array $orderBy = null)
 * @method PasswordReminder[]    findAll()
 * @method PasswordReminder[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PasswordReminderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PasswordReminder::class);
    }

    /**
     * @param string $token
     * @return PasswordReminder|null
     */
    public function findPasswordReminderByToken(string $token): ?PasswordReminder
    {
        $passwordReminderArr = $this->createQueryBuilder('p')
            ->andWhere('p.used = :usedStatus')
            ->andWhere('p.hash = :token')
            ->andWhere('p.expireDate >= :currDateTime')
            ->setParameter('token', $token)
            ->setParameter('usedStatus', false)
            ->setParameter('currDateTime', new \DateTime())
            ->setMaxResults(1)
            ->getQuery()
            ->getResult()
        ;

        if (count($passwordReminderArr) > 0) {
            return $passwordReminderArr[0];
        }

        return null;
    }

    /*
    public function findOneBySomeField($value): ?PasswordReminder
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
