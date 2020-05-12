<?php

namespace App\Controller;

use App\Entity\QuestionAnswer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ResultsController extends AbstractController
{
    /**
     * @Route("/rezultatai", name="app_results")
     */
    public function index()
    {
        $conn = $this->getDoctrine()->getConnection();


        $queryTopUsersWeekly = 'SELECT user_id, user.username, COUNT(user_id) as count
                FROM question_answer
                JOIN user ON user.id = question_answer.user_id
                WHERE user_id IS NOT NULL AND
                question_answer.time_answered >= DATE(NOW()) - INTERVAL 7 DAY
                GROUP BY user_id
                ORDER BY count DESC, user.username DESC
                LIMIT 5';

        $queryTopUsersMonthly = 'SELECT user_id, user.username, COUNT(user_id) as count
                FROM question_answer
                JOIN user ON user.id = question_answer.user_id
                WHERE user_id IS NOT NULL AND
                question_answer.time_answered >= DATE(NOW()) - INTERVAL 30 DAY
                GROUP BY user_id
                ORDER BY count DESC, user.username DESC
                LIMIT 5';


        $resultTopUsersGlobal = $this->getGlobalUsers();
        $statement = $conn->prepare($queryTopUsersWeekly);
        $statement->execute();
        $resultTopUsersWeekly = $statement->fetchAll();
        $statement = $conn->prepare($queryTopUsersMonthly);
        $statement->execute();
        $resultTopUsersMonthly = $statement->fetchAll();

        $entityManager = $this->getDoctrine()->getManager();
        $questionAnswerCount = $entityManager->getRepository(QuestionAnswer::class)->findAll();

        return $this->render('results/index.html.twig', [
            'globalTopUsers' => $resultTopUsersGlobal,
            'weeklyTopUsers' => $resultTopUsersWeekly,
            'monthlyTopUsers' => $resultTopUsersMonthly,
            'allQuestionAnswersCount' => count($questionAnswerCount),
        ]);
    }

    public function getGlobalUsers()
    {
        $conn = $this->getDoctrine()->getConnection();

        $queryTopUsersGlobal = 'SELECT user_id, user.username, COUNT(user_id) as count
                FROM question_answer
                JOIN user ON user.id = question_answer.user_id
                WHERE user_id IS NOT NULL
                GROUP BY user_id
                ORDER BY count DESC, user.username DESC
                LIMIT 5';

        $statement = $conn->prepare($queryTopUsersGlobal);
        $statement->execute();
        return $statement->fetchAll();
    }
}
