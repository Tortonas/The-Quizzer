<?php

namespace App\Controller\Api;

use App\Entity\Question;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CurrentQuestionCheckerController extends AbstractController
{
    /**
     * @Route("/api/question", name="api_get_current_question")
     */
    public function index()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $currentQuestion = $entityManager->getRepository(Question::class)->findOneBy(array('active' => 1));

        return new Response($currentQuestion->getQuestion());
    }
}
