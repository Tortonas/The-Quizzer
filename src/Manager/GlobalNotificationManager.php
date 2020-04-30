<?php


namespace App\Manager;


use App\Entity\GlobalNotification;
use Doctrine\ORM\EntityManagerInterface;

class GlobalNotificationManager
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getAllAvailableNotifications()
    {
        return $this->entityManager->getRepository(GlobalNotification::class)->findNewerNotificationsThan(new \DateTime());
    }
}
