<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EmailController extends AbstractController
{
    /**
     * @var \Swift_Mailer
     */
    private \Swift_Mailer $mailer;

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * EmailController constructor.
     * @param \Swift_Mailer $mailer
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(\Swift_Mailer $mailer, EntityManagerInterface $entityManager)
    {
        $this->mailer = $mailer;
        $this->entityManager = $entityManager;
    }

    /**
     * @param mixed $arrayOfGlobalResultsGlobalPrevious
     * @param mixed $arrayOfGlobalResultsGlobalAfter
     */
    public function sendMessageYouHaveBeenPassed($arrayOfGlobalResultsGlobalPrevious, $arrayOfGlobalResultsGlobalAfter): void
    {
        $usersThatGoingUp = [];
        $usersThatGoingDown = [];

        foreach ($arrayOfGlobalResultsGlobalPrevious as $key => $user) {
            for ($i = 0; $i < count($arrayOfGlobalResultsGlobalPrevious); $i++) {
                if ($user['user_id'] === $arrayOfGlobalResultsGlobalAfter[$i]['user_id']) {
                    if ($key !== $i) {
                        if ($key > $i) {
                            $usersThatGoingUp[] = $this->entityManager->getRepository(User::class)->find($user['user_id']);
                        } else {
                            $usersThatGoingDown[] = $this->entityManager->getRepository(User::class)->find($arrayOfGlobalResultsGlobalAfter[$i]['user_id']);
                        }
                    }
                }
            }
        }

        $this->sendEmailToUsersWhoAreGoingUpInGlobalScoreboard($usersThatGoingUp);
        $this->sendEmailToUsersWhoAreGoingDownInGlobalScoreboard($usersThatGoingDown);
    }

    /**
     * @param User[] $usersThatAreGoingUp
     */
    private function sendEmailToUsersWhoAreGoingUpInGlobalScoreboard(array $usersThatAreGoingUp): void
    {
        foreach ($usersThatAreGoingUp as $user) {
            if ($user->getEmailSubscription()) {
                $message = (new \Swift_Message('QUIZZER - TU APLENKEI KITA NARI! SAUNUOLIS!'))
                    ->setFrom(['quizzerlt@gmail.com' => 'Ponas Quizzer'])
                    ->setTo($user->getEmail())
                    ->setBody(
                        $this->renderView(
                            'emails/took_lead.html.twig',
                            ['name' => $user->getUsername()]
                        ),
                        'text/html'
                    );

                $this->mailer->send($message);
            }
        }
    }

    /**
     * @param User[] $usersThatAreGoingDown
     */
    private function sendEmailToUsersWhoAreGoingDownInGlobalScoreboard(array $usersThatAreGoingDown): void
    {
        foreach ($usersThatAreGoingDown as $user) {
            if ($user->getEmailSubscription()) {
                $message = (new \Swift_Message('QUIZZER - TU BUVAI APLENKTAS!'))
                    ->setFrom(['quizzerlt@gmail.com' => 'Ponas Quizzer'])
                    ->setTo($user->getEmail())
                    ->setBody(
                        $this->renderView(
                            'emails/surpassed.html.twig',
                            ['name' => $user->getUsername()]
                        ),
                        'text/html'
                    );

                $this->mailer->send($message);
            }
        }
    }

    /**
     * @param User $user
     */
    public function sendMarketingEmail(User $user): void
    {
        if ($user->getEmailSubscription()) {
            $message = (new \Swift_Message('The Quizzer - ar vis dar pameni Žalgirio mūšio datą?'))
                ->setFrom(['quizzerlt@gmail.com' => 'Ponas Quizzer'])
                ->setTo($user->getEmail())
                ->setBody(
                    $this->renderView(
                        'emails/marketing.html.twig',
                        ['name' => $user->getUsername()]
                    ),
                    'text/html'
                );

            $this->mailer->send($message);
            $user->setLastTimeGotEmail(new \DateTime());
        }
    }
}
