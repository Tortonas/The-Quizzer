<?php

namespace App\Controller;

use App\Entity\QuestionAnswer;
use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\LoginFormAuthenticator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/register", name="app_register")
     * @throws \Exception
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, LoginFormAuthenticator $authenticator): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $user->setRegisterAt(new \DateTime(date('Y-m-d')));
            $entityManager = $this->getDoctrine()->getManager();

            // checking if new registering user email is unique

            $userWithSameEmail = $entityManager->getRepository(User::class)->findOneBy(array('email' => $user->getEmail()));

            $questionsWithThatNickname = $entityManager->getRepository(QuestionAnswer::class)->
                findBy(array('username' => $form->get('username')->getData(),
                             'user' => null));
            for($i = 0; $i < count($questionsWithThatNickname); $i++)
            {
                $questionsWithThatNickname[$i]->setUser($user);
            }

            if($userWithSameEmail != null)
            {
                $this->addFlash('danger', 'Atsiprašome, bet šis email yra užimtas!');
            }
            else
            {
                $entityManager->persist($user);
                $entityManager->flush();

                // do anything else you need here, like send an email

                return $guardHandler->authenticateUserAndHandleSuccess(
                    $user,
                    $request,
                    $authenticator,
                    'main' // firewall name in security.yaml
                );
            }
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
