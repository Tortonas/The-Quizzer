<?php

namespace App\Controller;

use App\Entity\Question;
use App\Entity\QuestionAnswer;
use App\Form\EmptyFormType;
use Doctrine\ORM\EntityManager;
use http\Url;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="app_home")
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function index(Request $request)
    {
        date_default_timezone_set('Europe/Vilnius');
        $closePopupForm = $this->createForm(EmptyFormType::class);
        $closePopupForm->handleRequest($request);

        $showWelcomeScreen = true;

        if($closePopupForm->isSubmitted() && $request->get('closeBtn'))
        {
            $cookie = new Cookie('closeWelcomeScreen', 'true', strtotime('now + 1 year'));
            $response = new Response();
            $response->headers->setCookie($cookie);
            $response->send();
            $showWelcomeScreen = false;
        }

        $customNicknameForm = $this->createForm(EmptyFormType::class);

        $customNicknameForm->handleRequest($request);

        if($customNicknameForm->isSubmitted() && $request->get('setNicknameBtn'))
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


        $submitAnswerForm = $this->createForm(EmptyFormType::class);
        $submitAnswerForm->handleRequest($request);

        $entityManager = $this->getDoctrine()->getManager();
        /** @var Question $currentQuestion */
        $currentQuestion = $entityManager->getRepository(Question::class)->findOneBy(array(
            'active' => 1));
        $currentDateTime = date('Y-m-d H:i:s');

        if($submitAnswerForm->isSubmitted() && $request->get('answerBtn'))
        {
            $plainAnswer = $currentQuestion->getAnswer();
            $plainAnswerSubmission = $request->get('answer');

            $lithuanianLetters = array('ą', 'č', 'ę', 'ė', 'į', 'š', 'ų', 'ū', 'ž', 'Ą', 'Č', 'Ę', 'Ė', 'Į', 'Š', 'Ų', 'Ū', 'Ž');
            $latinLetters = array('a', 'c', 'e', 'e', 'i', 's', 'u', 'u', 'z', 'a', 'c', 'e', 'e', 'i', 's', 'u', 'u', 'z');

            $plainAnswerSubmissionSplit = str_split($plainAnswerSubmission);

            $plainAnswerSubmission = str_replace($lithuanianLetters, $latinLetters, $plainAnswerSubmission);

            $plainAnswer = strtolower($plainAnswer);
            $plainAnswerSubmission = strtolower($plainAnswerSubmission);

            if(count($plainAnswerSubmissionSplit) != strlen($plainAnswerSubmission) && count($plainAnswerSubmissionSplit) != 1 && strlen($plainAnswerSubmission) != 0)
            {
                $this->addFlash('info-submit-form', 'Hey psst, atsakymus gali rašyti ir be lietuviškų raidžių (gali ir su) 😇 Jeigu jis tiks, jis bus užskaitytas.');
            }

            if($plainAnswer == $plainAnswerSubmission)
            {
                if($this->getUser() == null)
                {
                    $this->addFlash('info-submit-form', 'Hey psst, jeigu būtum prisiregistravęs, būtum gavęs tašką. Kodėl neužsiregistravus? Prisijungti gali ir su Google 🤗');
                }
                $this->addFlash('success-submit-form', 'Atsakymas teisingas! Naujas klausimas užkrautas 😉👍');
                $newQuestionAnswer = new QuestionAnswer();
                $newQuestionAnswer->setUser($this->getUser());
                $newQuestionAnswer->setTimeAnswered(new \DateTime($currentDateTime));
                if($this->getUser() != null)
                    $newQuestionAnswer->setUsername($this->getUser()->getUsername());
                else
                    $newQuestionAnswer->setUsername($request->cookies->get('username'));
                $newQuestionAnswer->setQuestion($currentQuestion);

                $this->setNewQuestion($entityManager, $currentDateTime, $currentQuestion);

                $entityManager->persist($newQuestionAnswer);
                $entityManager->flush();
            }
            else
            {
                $this->addFlash('danger-submit-form', 'Atsakymas neteisingas!');
            }
        }



        // Jeigu true, tada keiciam klausima i nauja.
        $currentQuestionModifyTime = $currentQuestion->getTimeModified()->getTimestamp()+(60*3);
        $currentQuestionModifyTime = date("Y-m-d H:i:s", $currentQuestionModifyTime);

        if($currentQuestionModifyTime < $currentDateTime)
        {
            $this->setNewQuestion($entityManager, $currentDateTime, $currentQuestion);
        }

        $question = $entityManager->getRepository(Question::class)->findBy(array('active' => 1), array('timeModified' => 'ASC'), 1);
        $showQuestion = false;

        if($this->isGranted('ROLE_USER') || $request->cookies->get('username') != null)
        {
            $showQuestion = true;
        }

        $lastQuestionAnswerer = $entityManager->getRepository(QuestionAnswer::class)->findOneBy(array(), array('id' => 'DESC'));

        // This checks if previous question answerer (user) has a valid account, then his name will be displayed as link.


        $lastQuestionAnsweredWhen = $this->calculateHowMuchTimeAgo(microtime(true) - $lastQuestionAnswerer->getTimeAnswered()->getTimeStamp());

        $lastQuestionAnswererUserId = -1;

        if($lastQuestionAnswerer->getUser() != null)
        {
            $lastQuestionAnswererUserId = $lastQuestionAnswerer->getUser()->getId();
        }

        return $this->render('home/index.html.twig', [
            'closePopupForm' => $closePopupForm->createView(),
            'question' => $question,
            'setCustomNicknameForm' => $customNicknameForm->createView(),
            'showQuestion' => $showQuestion,
            'submitAnswerForm' => $submitAnswerForm->createView(),
            'showWelcomeScreen' => $showWelcomeScreen,
            'lastQuestionAnswerer' => $lastQuestionAnswerer->getUsername(),
            'lastQuestionAnswererUserId' => $lastQuestionAnswererUserId,
            'lastQuestionAnsweredWhen' => $lastQuestionAnsweredWhen,
        ]);
    }

    public function setNewQuestion($entityManager, $currentDateTime, $currentQuestion)
    {
        $newQuestionArray = $entityManager->getRepository(Question::class)->findBy(array('active' => 0), array('timeModified' => 'ASC'), 1);
        $newQuestion = $newQuestionArray[0];
        /** @var Question $newQuestion */
        $newQuestion->setActive(1);
        $newQuestion->setTimeModified(new \DateTime($currentDateTime));

        /** @var Question $currentQuestion */
        $currentQuestion->setActive(0);
        $currentQuestion->setTimeModified(new \DateTime($currentDateTime));

        /** @var EntityManager $entityManager */
        $entityManager->persist($newQuestion);
        $entityManager->persist($currentQuestion);
        $entityManager->flush();
    }

    public function calculateHowMuchTimeAgo($since)
    {
        $chunks = array(
            array(60 * 60 * 24 * 365 , 'metus'),
            array(60 * 60 * 24 * 30 , 'mėnesį (-ius)'),
            array(60 * 60 * 24 * 7, 'savaitę (-as)'),
            array(60 * 60 * 24 , 'dieną (-as)'),
            array(60 * 60 , 'valandą (-as)'),
            array(60 , 'minutę (-es)'),
            array(1 , 'sekundes (-ių)')
        );

        for ($i = 0, $j = count($chunks); $i < $j; $i++) {
            $seconds = $chunks[$i][0];
            $name = $chunks[$i][1];
            if (($count = floor($since / $seconds)) != 0) {
                break;
            }
        }

        $print = ($count == 1) ? '1 '.$name : "$count {$name}";

        return $print;
    }
}
