<?php

namespace App\Controller;

use App\Entity\PasswordReminder;
use App\Entity\User;
use App\Form\RemindPasswordFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ForgetPasswordController extends AbstractController
{
    private EmailController $emailController;

    /**
     * ForgetPasswordController constructor.
     * @param EmailController $emailController
     */
    public function __construct(EmailController $emailController)
    {
        $this->emailController = $emailController;
    }

    /**
     * @Route("/forget/password", name="forget_password")
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $remindPasswordForm = $this->createForm(RemindPasswordFormType::class);

        $remindPasswordForm->handleRequest($request);

        if ($remindPasswordForm->isSubmitted() && $remindPasswordForm->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $inputEmail = $remindPasswordForm->getData()['email'];

            /** @var User|null $user */
            $user = $entityManager->getRepository(User::class)->findOneBy([
                'email' => $inputEmail
            ]);

            if ($user) {
                $passwordToken = $this->emailController->buildEmailHash();
                $this->generatePasswordToken($user, $passwordToken);
                $this->emailController->sendPasswordReminder($user, $passwordToken);
            }
        }

        return $this->render('forget_password/index.html.twig', [
            'form' => $remindPasswordForm->createView(),
            'formSubmitted' => $remindPasswordForm->isSubmitted()
        ]);
    }

    /**
     * @Route("/reset/password/{reminderToken}", name="reset_password")
     * @param string $reminderToken
     * @return Response
     */
    public function resetPassword(string $reminderToken)
    {
        /** @var PasswordReminder|null $passwordReminder */
        $passwordReminder = $this->getDoctrine()->getManager()->getRepository(PasswordReminder::class)
            ->findPasswordReminderByToken($reminderToken);

        if ($passwordReminder) {
            $newPassword = bin2hex(random_bytes(4));
            $newPasswordEncrypted = password_hash($newPassword, PASSWORD_ARGON2ID);

            $passwordReminder->getUser()->setPassword($newPasswordEncrypted);
            $passwordReminder->setUsed(true);
            $this->getDoctrine()->getManager()->flush();

            $this->emailController->sendGeneratedPassword($passwordReminder->getUser(), $newPassword);

            return $this->render('forget_password/reset_email.html.twig', [
                'response' => 'success',
                'email' => $passwordReminder->getUser()->getEmail(),
            ]);
        }

        return $this->render('forget_password/reset_email.html.twig', [
            'response' => 'error',
            'email' => null
        ]);
    }

    private function generatePasswordToken(User $user, string $passwordToken): void
    {
        $nowDateTime = new \DateTimeImmutable();
        $dateTimePlusOneHour = $nowDateTime->modify('+1 hour');
        $passwordReminderToken = new PasswordReminder();
        $passwordReminderToken->setDate($nowDateTime)
            ->setUser($user)
            ->setHash($passwordToken)
            ->setUsed(false)
            ->setExpireDate($dateTimePlusOneHour);

        $this->getDoctrine()->getManager()->persist($passwordReminderToken);
        $this->getDoctrine()->getManager()->flush();
    }
}
