<?php

namespace App\Controller\Api;

use App\Controller\HomeController;
use App\Entity\Question;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CurrentQuestionCheckerController extends AbstractController
{
    private HomeController $homeController;

    public function __construct(HomeController $homeController)
    {
        $this->homeController = $homeController;
    }

    /**
     * @Route("/api/question", name="api_get_current_question")
     */
    public function index()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $currentQuestion = $entityManager->getRepository(Question::class)->findOneBy(array('active' => 1));

        return new Response($currentQuestion->getQuestion());
    }

    /**
     * @Route("/api/reload", name="api_reload_question_if_needed")
     */
    public function reloadQuestionIfNeeded()
    {
        $currentDateTime = date('Y-m-d H:i:s');
        $entityManager = $this->getDoctrine()->getManager();
        $currentQuestion = $entityManager->getRepository(Question::class)->findOneBy(array(
            'active' => 1));

        // If true, then changing questions to new one.
        $currentQuestionModifyTime = $currentQuestion->getTimeModified()->getTimestamp()+(60*3);
        $currentQuestionModifyTime = date("Y-m-d H:i:s", $currentQuestionModifyTime);

        if($currentQuestionModifyTime < $currentDateTime)
        {
            $this->homeController->setNewQuestion($entityManager, $currentDateTime, $currentQuestion);
        }

        return new Response();
    }
}
