<?php

namespace App\Controller;

use App\Entity\Question;
use App\Entity\QuestionAnswer;
use App\Form\EmptyFormType;
use Doctrine\ORM\EntityManager;
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

        if($closePopupForm->isSubmitted() && $request->get('closeBtn'))
        {
            $cookie = new Cookie('closeWelcomeScreen', 'true', strtotime('now + 1 year'));
            $response = new Response();
            $response->headers->setCookie($cookie);
            $response->send();
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
                // TODO: Pachekinti ar toks useris neegzistuoja, jeigu egzistuoja neleisti tokio nick deti.
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
            echo $request->get('answer');
            if($currentQuestion->getAnswer() == $request->get('answer'))
            {
                $this->addFlash('success-submit-form', 'Atsakymas teisingas!');
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
        $currentQuestionModifyTime = $currentQuestion->getTimeModified()->getTimestamp()+(60*20);
        $currentQuestionModifyTime = date("Y-m-d H:i:s", $currentQuestionModifyTime);

        //echo $currentQuestionModifyTime." <- dabartinio klausimo<br>";
        //echo $currentDateTime;
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

        return $this->render('home/index.html.twig', [
            'closePopupForm' => $closePopupForm->createView(),
            'question' => $question,
            'setCustomNicknameForm' => $customNicknameForm->createView(),
            'showQuestion' => $showQuestion,
            'submitAnswerForm' => $submitAnswerForm->createView(),
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
}
