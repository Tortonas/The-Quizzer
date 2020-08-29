<?php

namespace App\DataFixtures;

use App\Entity\FreqAskedQuestion;
use App\Entity\Question;
use App\Entity\QuestionAnswer;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    private \Doctrine\Persistence\ObjectManager $manager;

    public function load(\Doctrine\Persistence\ObjectManager $manager)
    {
        $this->manager = $manager;
        if ($this->doINeedToUploadFixtures()) {
            $user = $this->generateUser();
            $this->generateQuestionsWithAnswers($user);
            $this->generateFaqs();

            $manager->flush();
        }
    }

    private function doINeedToUploadFixtures(): bool
    {
        /** @var \Doctrine\ORM\EntityRepository */
        $userRepository = $this->manager->getRepository(User::class);
        /** @var \Doctrine\ORM\EntityRepository */
        $questionRepository = $this->manager->getRepository(Question::class);
        /** @var \Doctrine\ORM\EntityRepository */
        $questionAnswerRepository = $this->manager->getRepository(QuestionAnswer::class);
        /** @var \Doctrine\ORM\EntityRepository */
        $faqRepository = $this->manager->getRepository(FreqAskedQuestion::class);
        $userCount = $userRepository->count([]);
        $questionCount = $questionRepository->count([]);
        $questionAnswerCount = $questionAnswerRepository->count([]);
        $faqCount = $faqRepository->count([]);

        // If true, then I don't need to upload fixtures
        if ($userCount >= 1 && $questionCount >= 3 && $questionAnswerCount >= 1 && $faqCount  >= 3) {
            return false;
        }

        return true;
    }

    private function generateUser(): User
    {
        $user = new User();
        $user->setEmail('admin@admin.dev');
        $user->setEmailSubscription(true);
        $user->setLastTimeGotEmail(new \DateTime('2020-01-01'));
        // hashed password of this string -> admin
        $user->setPassword('$argon2id$v=19$m=65536,t=4,p=1$Z0dFckxiSDJSelpqc0I3Tg$VqckwJeFQ8JA3pOYN4664C9uejMbpTcYy7d+1++9eFQ');
        $user->setUsername('Admin');
        $user->setRegisterAt(new \DateTime());
        $user->setLastVisit(new \DateTime());
        $user->setRoles(['ROLE_ADMIN']);
        $this->manager->persist($user);

        $user = new User();
        $user->setEmail('user@user.dev');
        $user->setEmailSubscription(true);
        $user->setLastTimeGotEmail(new \DateTime('2020-01-01'));
        // hashed password of this string -> user
        $user->setPassword('$argon2id$v=19$m=65536,t=4,p=1$DCnChjDNGC4QmtrQWIv5mw$euEzmgQS8ry9vLWjIYi29sobJsa7WTRqDNmwa8h2VzY');
        $user->setUsername('User');
        $user->setRegisterAt(new \DateTime());
        $user->setLastVisit(new \DateTime());
        $user->setRoles([]);
        $this->manager->persist($user);

        return $user;
    }

    private function generateQuestionsWithAnswers(User $user): void
    {
        $question = new Question();
        $question->setActive(true);
        $question->setQuestion('Whats 2+2?');
        $question->setAnswer('4');
        $question->setTimeModified(new \DateTime('2020-01-01'));
        $this->manager->persist($question);

        $question = new Question();
        $question->setActive(false);
        $question->setQuestion('Whats 3+3?');
        $question->setAnswer('6');
        $question->setTimeModified(new \DateTime('2020-01-02'));
        $this->manager->persist($question);

        $question = new Question();
        $question->setActive(false);
        $question->setQuestion('Whats 5+5?');
        $question->setAnswer('10');
        $question->setTimeModified(new \DateTime('2020-01-03'));
        $this->manager->persist($question);

        $questionAnswer = new QuestionAnswer();
        $questionAnswer->setUsername('Admin');
        $questionAnswer->setUser($user);
        $questionAnswer->setQuestion($question);
        $questionAnswer->setTimeAnswered(new \DateTime());
        $this->manager->persist($questionAnswer);
    }

    private function generateFaqs(): void
    {
        for ($i = 1; $i <= 3; $i++) {
            $faq = new FreqAskedQuestion();
            $faq->setQuestion($i . ' FAQ Demo question');
            $faq->setAnswer($i . ' FAQ Demo answer');
            $this->manager->persist($faq);
        }
    }
}
