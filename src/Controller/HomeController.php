<?php

namespace App\Controller;

use App\Form\ClosePopupFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="app_home")
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $form = $this->createForm(ClosePopupFormType::class);
        $form->handleRequest($request);

        if($form->isSubmitted())
        {
            $cookie = new Cookie('closeWelcomeScreen', 'true', strtotime('now + 10 minutes'));
            $response = new Response();
            $response->headers->setCookie($cookie);
            $response->send();
        }

        return $this->render('home/index.html.twig', [
            'closePopupForm' => $form->createView(),
        ]);
    }
}
