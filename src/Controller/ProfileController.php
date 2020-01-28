<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    /**
     * @Route("/profilis", name="app_profile")
     * @IsGranted("ROLE_USER")
     */
    public function index()
    {
        $conn = $this->getDoctrine()->getConnection();
        $queryWeeklyAnswers = 'SELECT user_id, COUNT(user_id) as count
                FROM question_answer
                JOIN user ON user.id = question_answer.user_id
                WHERE user_id IS NOT NULL AND
                question_answer.time_answered >= DATE(NOW()) - INTERVAL 7 DAY AND
                user_id = '.$this->getUser()->getId().'
                GROUP BY user_id';

        $queryMonthlyAnswers = 'SELECT user_id, COUNT(user_id) as count
                FROM question_answer
                JOIN user ON user.id = question_answer.user_id
                WHERE user_id IS NOT NULL AND
                question_answer.time_answered >= DATE(NOW()) - INTERVAL 30 DAY AND
                user_id = '.$this->getUser()->getId().'
                GROUP BY user_id';

        $queryAllTimeAnswers = 'SELECT user_id, COUNT(user_id) as count
                FROM question_answer
                JOIN user ON user.id = question_answer.user_id
                WHERE user_id IS NOT NULL AND
                user_id = '.$this->getUser()->getId().'
                GROUP BY user_id';


        $statement = $conn->prepare($queryWeeklyAnswers);
        $statement->execute();
        $weeklyAnswers = $statement->fetchAll();

        $statement = $conn->prepare($queryMonthlyAnswers);
        $statement->execute();
        $monthlyAnswers = $statement->fetchAll();

        $statement = $conn->prepare($queryAllTimeAnswers);
        $statement->execute();
        $allTimeAnswers = $statement->fetchAll();

        if(empty($weeklyAnswers))
            $weeklyAnswers[0]['count'] = 0;
        if(empty($monthlyAnswers))
            $monthlyAnswers[0]['count'] = 0;
        if(empty($allTimeAnswers))
            $allTimeAnswers[0]['count'] = 0;

//        var_dump($weeklyAnswers);
//        die;

        return $this->render('profile/index.html.twig', [
            'user' => $this->getUser(),
            'weeklyAnswers' => $weeklyAnswers[0]['count'],
            'monthlyAnswers' => $monthlyAnswers[0]['count'],
            'allTimeAnswers' => $allTimeAnswers[0]['count'],
        ]);
    }

    /**
     * @Route("/profilis/keisti", name="app_profile_edit")
     * @IsGranted("ROLE_USER")
     * @param Request $request
     * @return Response
     */
    public function edit(Request $request)
    {
        if($request->get('v') == 'p')
        {
            return $this->render('profile/changePass.html.twig', [
                'user' => $this->getUser(),
            ]);
        }
        else if($request->get('v') == 'e')
        {
            return $this->render('profile/changeEmail.html.twig', [
                'user' => $this->getUser(),
            ]);
        }
    }
}
