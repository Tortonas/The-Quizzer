<?php

namespace App\Controller;

use App\Entity\GlobalNotification;
use App\Entity\Question;
use App\Entity\QuestionAnswer;
use App\Entity\User;
use App\Form\EmptyFormType;
use App\Helper\QuestionsHelper;
use App\Manager\ActivityManager;
use App\Manager\GlobalNotificationManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    private ActivityManager $activityManager;

    private GlobalNotificationManager $globalNotificationManager;

    private ResultsController $resultsController;

    private EmailController $emailController;

    public function __construct(
        ActivityManager $activityManager,
        GlobalNotificationManager $globalNotificationManager,
        ResultsController $resultsController,
        EmailController $emailController
    )
    {
        $this->activityManager = $activityManager;
        $this->globalNotificationManager = $globalNotificationManager;
        $this->resultsController = $resultsController;
        $this->emailController = $emailController;
    }

    /**
     * @Route("/", name="app_home")
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function index(Request $request): Response
    {
        date_default_timezone_set('Europe/Vilnius');

        // If User is logged, his `username` cookie will be his username.

        if($this->getUser() != null)
        {
            if($request->cookies->get('username') != $this->getUser()->getUsername())
            {
                $cookie = new Cookie('username', $this->getUser()->getUsername(), strtotime('now + 1 year'));
                $response = new Response();
                $response->headers->setCookie($cookie);
                $response->send();
            }
        }

        // Handling Welcome screen
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


        // Handling custom nickname
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

        // Handling submitting answers!
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

            $plainAnswerSubmissionSplit = str_split($plainAnswerSubmission);

            $plainAnswerSubmission = QuestionsHelper::toLowerAndReplaceLetters($plainAnswerSubmission);

            // TODO: Make more normal lithuanian letter checker.
            if(count($plainAnswerSubmissionSplit) != strlen($plainAnswerSubmission) && count($plainAnswerSubmissionSplit) != 1 && strlen($plainAnswerSubmission) != 0)
            {
                $this->addFlash('info-submit-form', 'Hey psst, atsakymus gali rašyti ir be lietuviškų raidžių (gali ir su) 😇 Jeigu jis tiks, jis bus užskaitytas.');
            }

            $plainAnswer = QuestionsHelper::toLowerAndReplaceLetters($plainAnswer);

            if($plainAnswer == $plainAnswerSubmission)
            {
                $arrayOfGlobalResultsGlobalPrevious = $this->resultsController->getGlobalUsers();

                $this->addFlash('success-submit-form', 'Atsakymas teisingas! Naujas klausimas užkrautas 😉👍');
                $newQuestionAnswer = new QuestionAnswer();
                // If for example I'm anonymous with nick 'SANDRA' and nickname 'SANDRA' actually exists, so I will be earning point for real account.
                if($this->getUser() == null)
                {
                    $targetedUser = $entityManager->getRepository(User::class)->findOneBy(array('username' => $request->cookies->get('username')));
                    $newQuestionAnswer->setUser($targetedUser);
                    if($targetedUser == null)
                    {
                        $this->addFlash('info-submit-form', 'Hey psst, jeigu būtum prisiregistravęs, būtum gavęs tašką. Kodėl neužsiregistravus? Prisijungti gali ir su Google 🤗');
                    }
                }
                else
                {
                    $newQuestionAnswer->setUser($this->getUser());
                }
                // Changing answered question to the new one. Also modifying "modification date".
                $newQuestionAnswer->setTimeAnswered(new \DateTime($currentDateTime));
                if($this->getUser() != null)
                    $newQuestionAnswer->setUsername($this->getUser()->getUsername());
                else
                    $newQuestionAnswer->setUsername($request->cookies->get('username'));
                $newQuestionAnswer->setQuestion($currentQuestion);

                $this->setNewQuestion($entityManager, $currentDateTime, $currentQuestion);

                $entityManager->persist($newQuestionAnswer);
                $entityManager->flush();

                $arrayOfGlobalResultsGlobalAfter = $this->resultsController->getGlobalUsers();
                $this->emailController->sendMessageYouHaveBeenPassed($arrayOfGlobalResultsGlobalPrevious, $arrayOfGlobalResultsGlobalAfter);
            }
            else
            {
                if(strlen($plainAnswerSubmission) == 0)
                {
                    $this->addFlash('danger-submit-form', 'Pamiršote įvesti tekstą! 😊');
                }
                else
                {
                    $this->addFlash('danger-submit-form', 'Atsakymas neteisingas!');
                }
            }
        }

        // If true, then changing questions to new one.
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

        /** @var EntityRepository $questionAnswerRepository */
        $questionAnswerRepository = $entityManager->getRepository(QuestionAnswer::class);
        $lastQuestionAnswerer = $questionAnswerRepository->findOneBy(array(), array('id' => 'DESC'));

        // This checks if previous question answerer (user) has a valid account, then his name will be displayed as link.

        $lastQuestionAnsweredWhen = QuestionsHelper::calculateHowMuchTimeAgo(microtime(true) - $lastQuestionAnswerer->getTimeAnswered()->getTimeStamp());

        $lastQuestionAnswererUserId = -1;

        if($lastQuestionAnswerer->getUser() != null)
        {
            $lastQuestionAnswererUserId = $lastQuestionAnswerer->getUser()->getId();
        }

        // This gets previous ANSWERED or NOT ANSWERED question. Newest modified date + active = 0, it means thats the question.

        $previousQuestionArray = $entityManager->getRepository(Question::class)->findBy(array('active' => 0), array('timeModified' => 'DESC'), 1);
        $previousQuestion = $previousQuestionArray[0];

        /** @var GlobalNotification $globalNotifications */
        $globalNotifications = $this->globalNotificationManager->getAllAvailableNotifications();

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
            'previousQuestion' => $previousQuestion,
            'globalNotifications' => $globalNotifications
        ]);
    }

    public function setNewQuestion($entityManager, $currentDateTime, $currentQuestion): void
    {
        $newQuestionArray = $entityManager->getRepository(Question::class)->findBy(array('active' => false), array('timeModified' => 'ASC'), 1);
        $newQuestion = $newQuestionArray[0];
        /** @var Question $newQuestion */
        $newQuestion->setActive(true);
        $newQuestion->setTimeModified(new \DateTime($currentDateTime));

        /** @var Question $currentQuestion */
        $currentQuestion->setActive(false);
        $currentQuestion->setTimeModified(new \DateTime($currentDateTime));

        /** @var EntityManager $entityManager */
        $entityManager->persist($newQuestion);
        $entityManager->persist($currentQuestion);
        $entityManager->flush();
    }
}
