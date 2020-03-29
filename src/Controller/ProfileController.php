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
        $weeklyAnswers = $this->getQuestionCount($this->getUser()->getId(), 7);
        $monthlyAnswers = $this->getQuestionCount($this->getUser()->getId(), 30);
        $allTimeQuestions = $this->getQuestionCountAllTime($this->getUser()->getId());

        return $this->render('profile/index.html.twig', [
            'user' => $this->getUser(),
            'weeklyAnswers' => $weeklyAnswers,
            'monthlyAnswers' => $monthlyAnswers,
            'allTimeAnswers' => $allTimeQuestions,
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

            if($this->getUser()->getPassword() == 'google login' || $this->getUser()->getPassword() == 'facebook login')
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


            return $this->render('profile/change/changePass.html.twig', [
                'normalPasswordChangeForm' => $normalPasswordChangeForm
            ]);
        }
        else if($request->get('v') == 'e')
        {
            if($request->isMethod('POST'))
            {
                if (filter_var($request->get('newEmail'), FILTER_VALIDATE_EMAIL))
                {
                    $this->addFlash('success', 'Pakeista!');
                    $this->getUser()->setEmail($request->get('newEmail'));
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($this->getUser());
                    $entityManager->flush();
                }
                else
                {
                    $this->addFlash('danger', 'Blogai nurodytas naujas el. paštas!');
                }
            }
            return $this->render('profile/change/changeEmail.html.twig', [
                'user' => $this->getUser(),
            ]);
        }
        else {
            return $this->render('bundles/TwigBundle/Exception/error404.html.twig');
        }
    }

    /**
     * @Route("/profilis/{slug}", name="app_view_other_profile")
     * @param string $slug
     * @return Response
     */
    public function viewOtherProfile($slug)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $slugUser = $entityManager->getRepository(User::class)->find($slug);

        $weeklyAnswers = 0;
        $monthlyAnswers = 0;
        $allTimeQuestions = 0;

        if($slugUser != null)
        {
            $weeklyAnswers = $this->getQuestionCount($slugUser->getId(), 7);
            $monthlyAnswers = $this->getQuestionCount($slugUser->getId(), 30);
            $allTimeQuestions = $this->getQuestionCountAllTime($slugUser->getId());
        }

        return $this->render('profile/other profile/viewOtherProfile.html.twig', [
            'user' => $slugUser,
            'weeklyAnswers' => $weeklyAnswers,
            'monthlyAnswers' => $monthlyAnswers,
            'allTimeAnswers' => $allTimeQuestions,
        ]);
    }

    private function getQuestionCount($userId, $dayCount)
    {
        $conn = $this->getDoctrine()->getConnection();
        $query = 'SELECT user_id, COUNT(user_id) as count
                FROM question_answer
                JOIN user ON user.id = question_answer.user_id
                WHERE user_id IS NOT NULL AND
                question_answer.time_answered >= DATE(NOW()) - INTERVAL '.$dayCount.' DAY AND
                user_id = '.$userId.'
                GROUP BY user_id';

        $statement = $conn->prepare($query);
        $statement->execute();
        $answers = $statement->fetchAll();

        if(empty($answers))
            $answers[0]['count'] = 0;

        return $answers[0]['count'];
    }

    private function getQuestionCountAllTime($userId)
    {
        $conn = $this->getDoctrine()->getConnection();
        $query = 'SELECT user_id, COUNT(user_id) as count
                FROM question_answer
                JOIN user ON user.id = question_answer.user_id
                WHERE user_id IS NOT NULL AND
                user_id = '.$userId.'
                GROUP BY user_id';

        $statement = $conn->prepare($query);
        $statement->execute();
        $answers = $statement->fetchAll();

        if(empty($answers))
            $answers[0]['count'] = 0;

        return $answers[0]['count'];
    }
}
