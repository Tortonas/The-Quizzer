<?php

namespace App\Controller;

use App\Entity\QuestionAnswer;
use App\Entity\User;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class FacebookLoginController extends AbstractController
{
    /**
     * Link to this controller to start the "connect" process
     * @param ClientRegistry $clientRegistry
     *
     * @Route("/login/facebook", name="connect_facebook_start")
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function connectAction(ClientRegistry $clientRegistry)
    {
        return $clientRegistry
            ->getClient('facebook')
            ->redirect([
                'public_profile', 'email' // the scopes you want to access
            ])
            ;
    }

    /**
     * After going to Facebook, you're redirected back here
     * because this is the "redirect_route" you configured
     * in config/packages/knpu_oauth2_client.yaml
     *
     * @param Request $request
     * @param ClientRegistry $clientRegistry
     *
     * @Route("/login/facebook/check", name="connect_facebook_check")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function connectCheckAction(Request $request, ClientRegistry $clientRegistry)
    {
        $client = $clientRegistry->getClient('facebook');

        $entityManager = $this->getDoctrine()->getManager();
        $user = $client->fetchUser();
        $userInDatabase = $entityManager->getRepository(User::class)->findOneBy(array(
            'email' => $user->getEmail()));

        if($userInDatabase == null)
        {
            $userInDatabase = new User();
            $userInDatabase->setEmail($user->getEmail());
            $userInDatabase->setPassword('facebook login');

            $newUserNickname = $user->getName();
            $newUserNicknameCount = 0;
            $firstTime = true;

            while(true)
            {
                $tempUserUsername = $user->getName();
                $checkingUserForUniqueName = null;
                if($newUserNicknameCount == 0)
                {
                    $checkingUserForUniqueName = $entityManager->getRepository(User::class)->findOneBy(array(
                        'username' => $tempUserUsername));
                    $newUserNicknameCount++;
                }
                else
                {
                    $tempUserUsername = $tempUserUsername.$newUserNicknameCount;
                    $checkingUserForUniqueName = $entityManager->getRepository(User::class)->findOneBy(array(
                        'username' => $tempUserUsername));
                    $newUserNicknameCount++;
                }
                if($checkingUserForUniqueName == null)
                {
                    $newUserNickname = $tempUserUsername;
                    break;
                }
            }

            $userInDatabase->setUsername($newUserNickname);
            $userInDatabase->setRegisterAt(new \DateTime(date('Y-m-d')));

            $questionsWithThatNickname = $entityManager->getRepository(QuestionAnswer::class)->
            findBy(array('username' => $user->getName(),
                'user' => null));
            for($i = 0; $i < count($questionsWithThatNickname); $i++)
            {
                $questionsWithThatNickname[$i]->setUser($userInDatabase);
            }

            $entityManager->persist($userInDatabase);
            $entityManager->flush();
        }

        $token = new UsernamePasswordToken($userInDatabase, null, 'main', $userInDatabase->getRoles());
        $this->get('security.token_storage')->setToken($token);
        $this->get('session')->set('_security_main', serialize($token));

        $cookie = new Cookie('username', $userInDatabase->getUsername(), strtotime('now + 1 year'));
        $response = new Response();
        $response->headers->setCookie($cookie);
        $response->send();

        return $this->redirectToRoute('app_home');
    }
}
