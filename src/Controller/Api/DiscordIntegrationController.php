<?php

namespace App\Controller\Api;

use App\Controller\HomeController;
use App\Entity\DiscordUser;
use App\Entity\Question;
use App\Entity\QuestionAnswer;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DiscordIntegrationController extends AbstractController
{
    private HomeController $homeController;

    /**
     * DiscordIntegrationController constructor.
     * @param HomeController $homeController
     */
    public function __construct(HomeController $homeController)
    {
        $this->homeController = $homeController;
    }

    /**
     * @Route("/api/discord/set_name", name="discord_set_name")
     * @param Request $request
     * @return JsonResponse
     */
    public function discordSetName(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent());
        if ($data && property_exists($data, 'discord') && property_exists($data, 'name')) {
            $entityManager = $this->getDoctrine()->getManager();
            $discordUser = $entityManager->getRepository(DiscordUser::class)->findOneBy([
                'discordId' => $data->discord
            ]);
            $user = $entityManager->getRepository(User::class)->findOneBy([
                'username' => $data->name
            ]);

            if ($discordUser) {
                $discordUser->setName($data->name);
                $discordUser->setUser($user);
            } else {
                $discordUser = new DiscordUser();
                $discordUser->setName($data->name);
                $discordUser->setDiscordId($data->discord);
                $discordUser->setUser($user);
                $entityManager->persist($discordUser);
            }

            $entityManager->flush();

            if ($user) {
                return $this->json('Slapyvardis nustatytas!');
            } else {
                return $this->json('Toks slapyvardis neegzistuoja sistemoje, bet vistiek gali atsakinėti į klausimus. Neprarask taškų atsakinėdamas ir užsiregistruok https://quizzer.lt/register');
            }
        }

        return $this->failedToDeserializeJSON();
    }

    /**
     * @Route("/api/discord/answer_question", name="discord_answer_question")
     * @param Request $request
     * @return JsonResponse
     */
    public function discordAnswerQuestion(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent());
        if ($data && property_exists($data, 'discord') && property_exists($data, 'answer')) {
            $entityManager = $this->getDoctrine()->getManager();
            $discordUser = $entityManager->getRepository(DiscordUser::class)->findOneBy([
                'discordId' => $data->discord
            ]);

            if ($discordUser) {
                $currentQuestion = $this->getCurrentQuestion();
                return $this->checkIfAnswerIsCorrect($currentQuestion, $data, $discordUser);
            } else {
                return $this->json('Pradžioje nusistatyk slapvyvardį - !setname Slapyvardis');
            }
        }

        return $this->failedToDeserializeJSON();
    }

    /**
     * @Route("/api/discord/current_question", name="discord_current_question")
     * @return JsonResponse
     */
    public function discordCurrentQuestion(): JsonResponse
    {
        $currentQuestion = $this->getCurrentQuestion();
        $entityManager = $this->getDoctrine()->getManager();

        $currentQuestionModifyTime = $currentQuestion->getTimeModified()->getTimestamp()+(60*3);
        $currentQuestionModifyTime = date("Y-m-d H:i:s", $currentQuestionModifyTime);

        $currentDateTimeString = date('Y-m-d H:i:s');
        if($currentQuestionModifyTime < $currentDateTimeString)
        {
            $this->homeController->setNewQuestion($entityManager, $currentDateTimeString, $currentQuestion);
        }

        $currentQuestion = $this->getCurrentQuestion();
        $secondsTillNewQuestion = $this->whenQuestionWillResetSeconds($currentQuestion);

        $responseText = 'Klausimas - ' . $currentQuestion->getQuestion() . '. Klausimas atsinaujins po '
            . $secondsTillNewQuestion . ' sekundžių.';

        return $this->json($responseText);
    }

    /**
     * @Route("/api/discord/skip_question", name="discord_skip_question")
     * @return JsonResponse
     */
    public function discordSkipQuestion(): JsonResponse
    {
        $entityManager = $this->getDoctrine()->getManager();

        $currentDateTimeString = date('Y-m-d H:i:s');

        $currentQuestion = $this->getCurrentQuestion();
        $secondsTillNewQuestion = $this->whenQuestionWillResetSeconds($currentQuestion) - 150;

        if ($secondsTillNewQuestion >= 0) {
            return $this->json('Nepraėjo 30 sekundžių. Skipinti negalima. Dar liko - ' . $secondsTillNewQuestion . 'sec.');
        } else {
            $this->homeController->setNewQuestion($entityManager, $currentDateTimeString, $currentQuestion);
            return $this->json('Praeitas klausimas praleistas! Užkrautas naujas! Atsakymas buvo - ' . $currentQuestion->getAnswer());
        }
    }

    /**
     * @Route("/api/discord/prev_question_answer", name="discord_prev_question_answer")
     * @return JsonResponse
     */
    public function discordPrevQuestion(): JsonResponse
    {
        $entityManager = $this->getDoctrine()->getManager();
        $previousQuestionArray = $entityManager->getRepository(Question::class)->findBy(array('active' => 0), array('timeModified' => 'DESC'), 1);
        $previousQuestion = $previousQuestionArray[0];

        return $this->json('Praeitas klausimas - ' . $previousQuestion->getQuestion() . ' (Ats: ' . $previousQuestion->getAnswer() . ')');
    }

    private function failedToDeserializeJSON(): JsonResponse
    {
        return $this->json('Bad JSON data, failed to deserialize')->setStatusCode(400);
    }

    private function whenQuestionWillResetSeconds(Question $currentQuestion): int
    {
        $currentDateTime = new \DateTime();
        return $currentQuestion->getTimeModified()->getTimestamp()+(60*3) - $currentDateTime->getTimestamp();
    }

    private function getCurrentQuestion(): ?Question
    {
        return $this->getDoctrine()->getManager()->getRepository(Question::class)->findOneBy(array('active' => 1));
    }

    private function checkIfAnswerIsCorrect(Question $currentQuestion, \stdClass $data, DiscordUser $discordUser): JsonResponse
    {
        if ($currentQuestion->getAnswer() === $data->answer) {
            $entityManager = $this->getDoctrine()->getManager();

            $questionAnswer = new QuestionAnswer();
            $questionAnswer->setUser($discordUser->getUser());
            $questionAnswer->setQuestion($currentQuestion);
            $questionAnswer->setTimeAnswered(new \DateTime());
            $questionAnswer->setUsername($discordUser->getName());
            $entityManager->persist($questionAnswer);

            $this->homeController->setNewQuestion($entityManager, date('Y-m-d H:i:s'), $currentQuestion);

            return $this->json('Atspėjai! ' . $discordUser->getName() . ' gauna tašką.');
        } else {
            return $this->json('Deja, neteisingai.');
        }
    }
}
