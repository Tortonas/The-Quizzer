<?php

namespace App\Controller;

use App\Entity\User;
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
            $normalPasswordChangeForm = true; // true - normal; false - google login and password wasn't set

            if($this->getUser()->getPassword() == 'google login')
            {
                $normalPasswordChangeForm = false;
                if($request->isMethod('POST'))
                {
                    if($request->get('newPasswordGoogle') == $request->get('repeatPasswordGoogle') && strlen($request->get('newPasswordGoogle')) >= 3)
                    {
                        /** @var User $currentUser */
                        $currentUser = $this->getUser();

                        $newPassword = password_hash($request->get('newPasswordGoogle'), PASSWORD_ARGON2ID);

                        $currentUser->setPassword($newPassword);
                        $entityManager = $this->getDoctrine()->getManager();
                        $entityManager->persist($currentUser);
                        $entityManager->flush();

                        $this->addFlash('success', 'Naujas slaptažodis nustatytas!');
                    }
                    else
                    {
                        $this->addFlash('danger', 'Nauji slaptažodžiai nesutampa arba yra per trumpi!');
                    }
                }
            }
            else
            {
                if($request->isMethod('POST'))
                {
                    if(password_verify($request->get('oldPassword'), $this->getUser()->getPassword()) == 1
                        && $request->get('newPassword') == $request->get('repeatPassword') && strlen($request->get('newPassword')) >= 3)
                    {
                        /** @var User $currentUser */
                        $currentUser = $this->getUser();

                        $newPassword = password_hash($request->get('newPassword'), PASSWORD_ARGON2ID);

                        $currentUser->setPassword($newPassword);
                        $entityManager = $this->getDoctrine()->getManager();
                        $entityManager->persist($currentUser);
                        $entityManager->flush();

                        $this->addFlash('success', 'Slaptažodis pakeistas!');
                    }
                    else
                    {
                        $this->addFlash('danger', 'Blogas senas slaptažodis arba naujas slaptažodis neteisingai pakartotas!');
                    }
                }
            }


            return $this->render('profile/changePass.html.twig', [
                'normalPasswordChangeForm' => $normalPasswordChangeForm
            ]);
        }
        else if($request->get('v') == 'e')
        {
            if($request->isMethod('POST'))
            {
                echo $request->get('newEmail');
                $this->addFlash('danger', 'test');
            }
            return $this->render('profile/changeEmail.html.twig', [
                'user' => $this->getUser(),
            ]);
        }
    }
}
