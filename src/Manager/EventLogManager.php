<?php


namespace App\Manager;


use App\Entity\EventLog;
use App\Entity\User;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Cache\EntityHydrator;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class EventLogManager implements EventSubscriber
{
    const INSERT = 'insert';
    const UPDATE = 'update';
    const DELETE = 'delete';

    private EntityManager $entityManager;

    private TokenStorageInterface $tokenStorage;

    private $data = [];

    /**
     * EventLogManager constructor.
     * @param EntityManager $entityManager
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(EntityManager $entityManager, TokenStorageInterface $tokenStorage)
    {
        $this->entityManager = $entityManager;
        $this->tokenStorage = $tokenStorage;
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::postPersist,
            Events::preRemove,
            Events::postUpdate,
            Events::preUpdate,
        ];
    }

    public function postPersist(LifecycleEventArgs $args): void
    {
        if ($args->getObject() instanceof EventLog) {
            return;
        }

        $this->data = $this->objectToJson($args->getObject());
        $this->logActivity(self::INSERT, $args);
    }

    public function preRemove(LifecycleEventArgs $args): void
    {
        if ($args->getObject() instanceof EventLog) {
            return;
        }

        $this->data = $this->objectToJson($args->getObject());
        $this->logActivity(self::DELETE, $args);
    }

    public function postUpdate(LifecycleEventArgs $args): void
    {
        if ($args->getObject() instanceof EventLog) {
            return;
        }

        $this->logActivity(self::UPDATE, $args);
    }

    public function preUpdate(PreUpdateEventArgs $args): void
    {
        if ($args->getObject() instanceof EventLog) {
            return;
        }

        $this->data = $args->getEntityChangeSet(); //blogai iraso objektus, turetu objektu id rasyt
    }

    private function logActivity(string $action, LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        $eventLog = new EventLog();
        $eventLog
            ->setUser($this->getUser())
            ->setAction($action)
            ->setData($this->data)
            ->setEntity(get_class($entity));

        $this->entityManager->persist($eventLog);
        $this->entityManager->flush();
    }

    private function getUser(): ?UserInterface
    {
        $user = $this->tokenStorage->getToken()->getUser();

        if ($user instanceof User) {
            return $user;
        }

        return null;
    }

    private function objectToJson($object)
    {
        return []; //pafixint kad objekta i masyva transformintu normaliai
//        $jsonArray = [];
//
//        $objectReflection = new \ReflectionClass($object);
//        $objectProperties = $objectReflection->getProperties();
//        foreach ($objectProperties as $property) {
//            $jsonArray[$property->getName()] = $object->get[$property->getName()];
//        }
//
//        return $jsonArray;
    }
}
