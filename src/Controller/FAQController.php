<?php

namespace App\Controller;

use App\Entity\FreqAskedQuestion;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FAQController extends AbstractController
{
    /**
     * @Route("/duk", name="app_faq")
     */
    public function index(): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        $questions = $entityManager->getRepository(FreqAskedQuestion::class)->findAll();


        return $this->render('faq/index.html.twig', [
            'questions' => $questions,
        ]);
    }
}
