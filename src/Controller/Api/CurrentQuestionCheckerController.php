<?php

namespace App\Controller\Api;

use App\Controller\HomeController;
use App\Entity\Question;
use App\Entity\QuestionAnswer;
use App\Helper\QuestionsHelper;
use App\Repository\QuestionAnswerRepository;
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
        $currentQuestion = $entityManager->getRepository(Question::class)->findOneBy(array('active' => true));

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
            'active' => true
        ));

        // If true, then changing questions to new one.
        $currentQuestionModifyTime = $currentQuestion->getTimeModified()->getTimestamp()+(60*3);
        $currentQuestionModifyTime = date("Y-m-d H:i:s", $currentQuestionModifyTime);

        if($currentQuestionModifyTime < $currentDateTime)
        {
            $this->homeController->setNewQuestion($entityManager, $currentDateTime, $currentQuestion);
        }

        return new Response();
    }

    /**
     * @Route("/api/front_page_info", name="api_front_page_info")
     */
    public function apiFrontPage()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $currentQuestion = $entityManager->getRepository(Question::class)->findOneBy([
            'active' => true
        ]);

        $previousQuestionArray = $entityManager->getRepository(Question::class)->findBy(array('active' => 0), array('timeModified' => 'DESC'), 1);
        /** @var Question $previousQuestion */
        $previousQuestion = $previousQuestionArray[0];

        /** @var QuestionAnswerRepository $questionAnswerRepository */
        $questionAnswerRepository = $entityManager->getRepository(QuestionAnswer::class);
        $lastQuestionAnswerer = $questionAnswerRepository
            ->findOneBy(array(), array('id' => 'DESC'));

        // This checks if previous question answerer (user) has a valid account, then his name will be displayed as link.

        $timeTillNewQuestion = microtime(true) - $lastQuestionAnswerer->getTimeAnswered()->getTimeStamp();
        $lastQuestionAnsweredWhen = QuestionsHelper::calculateHowMuchTimeAgo($timeTillNewQuestion);

        $lastQuestionAnswererUserId = -1;

        if($lastQuestionAnswerer->getUser() != null)
        {
            $lastQuestionAnswererUserId = $lastQuestionAnswerer->getUser()->getId();
        }


        return $this->json([
            'currentQuestion' => $currentQuestion->getQuestion(),
            'previousQuestion' => $previousQuestion->getQuestion(),
            'previousQuestionAnswer' => $previousQuestion->getAnswer(),
            'lastQuestionAnswerer' => $lastQuestionAnswerer->getUsername(),
            'lastQuestionAnsweredWhen' => $lastQuestionAnsweredWhen,
            'lastQuestionAnswererUserId' => $lastQuestionAnswererUserId,
            'timeTillNewQuestion' => $timeTillNewQuestion
        ]);
    }
}
