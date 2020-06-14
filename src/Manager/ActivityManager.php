<?php


namespace App\Manager;


use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ActivityManager
{
    /* @var EntityManager $entityManager */
    private $entityManager;

    /* @var User $currentUser */
    private $currentUser;

    /**
     * ActivityManager constructor.
     * @param EntityManagerInterface $entityManager
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(EntityManagerInterface $entityManager, TokenStorageInterface $tokenStorage)
    {
        $this->entityManager = $entityManager;
        $this->currentUser = $tokenStorage->getToken()->getUser();
        $this->updateLastTimeLoggedIn();
    }

    /**
     * Every time this function is called, it will update lastVisit property on User entity
     */
    public function updateLastTimeLoggedIn()
    {
        date_default_timezone_set('Europe/Vilnius');
        if ($this->currentUser instanceof User) {
            $this->currentUser->setLastVisit(new \DateTime());
            try {
                $this->entityManager->persist($this->currentUser);
                $this->entityManager->flush();
            } catch (ORMException $e) {
                echo 'App\Manager\ActivityManager threw ORMException';
            }
        }
    }
}