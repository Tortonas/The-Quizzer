<?php


namespace App\Command;


use App\Controller\EmailController;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MarketingEmailCommand extends Command
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var EmailController
     */
    private $emailController;

    /**
     * @var string
     */
    protected static $defaultName = 'email:marketing-send';

    /**
     * MarketingEmailCommand constructor.
     * @param EntityManagerInterface $entityManager
     * @param EmailController $emailController
     * @param string|null $name
     */
    public function __construct(EntityManagerInterface $entityManager, EmailController $emailController, string $name = null)
    {
        parent::__construct($name);
        $this->entityManager = $entityManager;
        $this->emailController = $emailController;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $counter = 0;

        /** @var UserRepository $userRepository */
        $userRepository = $this->entityManager->getRepository(User::class);

        $users = $userRepository->getUsersThatNeedMarketingEmail();

        /** @var User $user */
        foreach ($users as $user) {
            $this->emailController->sendMarketingEmail($user);
            $counter++;
        }

        $this->entityManager->flush();

        $output->writeln($counter . ' Emails were sent!');
        return 0;
    }
}
