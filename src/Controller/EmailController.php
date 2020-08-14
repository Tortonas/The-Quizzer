<?php

namespace App\Controller;

use App\Entity\Email;
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
     * @Route("/cancel/email/{cancelHash}", name="cancel_hash")
     * @param string $cancelHash
     * @return Response
     */
    public function cancelEmail(string $cancelHash): Response
    {
        /** @var Email|null $email */
        $email = $this->entityManager->getRepository(Email::class)->findOneBy(
            [
                'cancelEmailSubHash' => $cancelHash,
                'cancelledEmailSub' => false
            ]
        );

        $user = null;

        if ($email) {
            $user = $email->getUser();
            $user->setEmailSubscription(false);
            $email->setCancelledEmailSub(true);
            $this->entityManager->flush();
            return $this->render('emails/cancel_email.html.twig', [
                'response' => 'success',
                'user' => $user
            ]);
        }

        return $this->render('emails/cancel_email.html.twig', [
            'response' => 'error',
            'user' => $user
        ]);
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
                $title = 'QUIZZER - TU APLENKEI KITA NARI! SAUNUOLIS!';
                $cancelEmailHash = $this->buildEmailHash();
                $message = (new \Swift_Message($title))
                    ->setFrom(['quizzerlt@gmail.com' => 'Ponas Quizzer'])
                    ->setTo($user->getEmail())
                    ->setBody(
                        $this->renderView(
                            'emails/took_lead.html.twig',
                            [
                                'name' => $user->getUsername(),
                                'cancelEmailHash' => $cancelEmailHash
                            ]
                        ),
                        'text/html'
                    );

                $this->mailer->send($message);

                $this->createEmailEntity($user, $title, $cancelEmailHash);
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
                $title = 'QUIZZER - TU BUVAI APLENKTAS!';
                $cancelEmailHash = $this->buildEmailHash();
                $message = (new \Swift_Message($title))
                    ->setFrom(['quizzerlt@gmail.com' => 'Ponas Quizzer'])
                    ->setTo($user->getEmail())
                    ->setBody(
                        $this->renderView(
                            'emails/surpassed.html.twig',
                            [
                                'name' => $user->getUsername(),
                                'cancelEmailHash' => $cancelEmailHash
                            ]
                        ),
                        'text/html'
                    );

                $this->mailer->send($message);

                $this->createEmailEntity($user, $title, $cancelEmailHash);
            }
        }
    }

    /**
     * @param User $user
     * @throws \Exception
     */
    public function sendMarketingEmail(User $user): void
    {
        if ($user->getEmailSubscription()) {
            $title = 'The Quizzer - kaip sekasi? Ar bepameni saulės mušio datą?';
            $cancelEmailHash = $this->buildEmailHash();
            $message = (new \Swift_Message($title))
                ->setFrom(['quizzerlt@gmail.com' => 'Ponas Quizzer'])
                ->setTo($user->getEmail())
                ->setBody(
                    $this->renderView(
                        'emails/marketing_purple.html.twig',
                        [
                            'name' => $user->getUsername(),
                            'cancelEmailHash' => $cancelEmailHash
                        ]
                    ),
                    'text/html'
                );

            $this->mailer->send($message);
            $user->setLastTimeGotEmail(new \DateTime());

            $this->createEmailEntity($user, $title, $cancelEmailHash);
        }
    }

    private function buildEmailHash(): string
    {
        return time() . bin2hex(random_bytes(16)) . 'quizzer';
    }

    private function createEmailEntity(User $user, string $title, string $cancelEmailHash): void
    {
        $email = new Email();
        $email->setUser($user)
            ->setEmail($user->getEmail())
            ->setDate(new \DateTime())
            ->setTitle($title)
            ->setCancelEmailSubHash($cancelEmailHash)
            ->setCancelledEmailSub(false);

        $this->entityManager->persist($email);
        $this->entityManager->flush();
    }
}
