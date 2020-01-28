<?php

namespace App\Controller;

use App\Entity\User;
use KnpU\OAuth2ClientBundle\Security\Authenticator\SocialAuthenticator;
use KnpU\OAuth2ClientBundle\Client\Provider\GoogleClient;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Provider\GoogleUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class GoogleLoginController extends AbstractController
{
    /**
     * @Route("/login/google", name="connect_google_start")
     * @param ClientRegistry $clientRegistry
     * @return Response
     */
    public function connectAction(ClientRegistry $clientRegistry)
    {
        return $clientRegistry
            ->getClient('google')
            ->redirect([
                'profile', 'email'
            ])
            ;
    }

    /**
     * After going to Google, you're redirected back here
     * because this is the "redirect_route" you configured
     * in config/packages/knpu_oauth2_client.yaml
     *
     * @param Request $request
     * @param ClientRegistry $clientRegistry
     *
     * @Route("/login/google/check", name="connect_google_check")
     * @return RedirectResponse
     */
    public function connectCheckAction(Request $request, ClientRegistry $clientRegistry)
    {
        /** @var GoogleUser $user */
        $client = $clientRegistry->getClient('google');

        try {
            // the exact class depends on which provider you're using

            $entityManager = $this->getDoctrine()->getManager();
            $user = $client->fetchUser();
            $userInDatabase = $entityManager->getRepository(User::class)->findOneBy(array(
                'email' => $user->getEmail()));

            if($userInDatabase == null)
            {
                $userInDatabase = new User();
                $userInDatabase->setEmail($user->getEmail());
                $userInDatabase->setPassword('google login');
                $userInDatabase->setUsername($user->getName());
                $userInDatabase->setRegisterAt(new \DateTime(date('Y-m-d')));

                $entityManager->persist($userInDatabase);
                $entityManager->flush();
            }

            $token = new UsernamePasswordToken($userInDatabase, null, 'main', $userInDatabase->getRoles());
            $this->get('security.token_storage')->setToken($token);
            $this->get('session')->set('_security_main', serialize($token));


            //echo $user->getFirstName().$user->getLastName().$user->getEmail();

            // do something with all this new power!
            // e.g. $name = $user->getFirstName();
            return $this->redirectToRoute('app_home');
            // ...
        } catch (IdentityProviderException $e) {
            // something went wrong!
            // probably you should return the reason to the user
            var_dump($e->getMessage()); die;
        }
    }
}
