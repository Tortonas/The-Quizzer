<?php

namespace App\Controller;

use App\Entity\Question;
use App\Form\ClosePopupFormType;
use App\Form\SetCustomNicknameFormType;
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
        $closePopupForm = $this->createForm(ClosePopupFormType::class);
        $closePopupForm->handleRequest($request);

        if($closePopupForm->isSubmitted())
        {
            $cookie = new Cookie('closeWelcomeScreen', 'true', strtotime('now + 1 year'));
            $response = new Response();
            $response->headers->setCookie($cookie);
            $response->send();
        }

        $customNicknameForm = $this->createForm(SetCustomNicknameFormType::class);

        $customNicknameForm->handleRequest($request);

        if($customNicknameForm->isSubmitted())
        {
            if($request->get('username') == null)
            {
                $this->addFlash('danger-nickname-form', 'Prašau, nurodykite savo slapyvardį');
            }
            else
            {
                $cookie = new Cookie('username', $request->get('username'), strtotime('now + 1 year'));
                $response = new Response();
                $response->headers->setCookie($cookie);
                $response->send();
                return $this->render('/home/nicknameSubmit.html.twig');
            }
        }

        $entityManager = $this->getDoctrine()->getManager();

        $question = $entityManager->getRepository(Question::class)->findBy(array(), array('timeAnswered' => 'ASC'), 1);

        $showQuestion = false;

        if($this->isGranted('ROLE_USER') || $request->cookies->get('username') != null)
        {
            $showQuestion = true;
        }

        return $this->render('home/index.html.twig', [
            'closePopupForm' => $closePopupForm->createView(),
            'question' => $question,
            'setCustomNicknameForm' => $customNicknameForm->createView(),
            'showQuestion' => $showQuestion,
        ]);
    }
}
