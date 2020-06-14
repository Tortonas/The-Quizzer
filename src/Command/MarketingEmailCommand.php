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
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var EmailController
     */
    private $emailController;

    protected static $defaultName = 'email:marketing-send';

    public function __construct(EntityManagerInterface $entityManager, EmailController $emailController, string $name = null)
    {
        parent::__construct($name);
        $this->entityManager = $entityManager;
        $this->emailController = $emailController;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $counter = 0;

        $users = $this->entityManager->getRepository(User::class)->getUsersThatNeedMarketingEmail();

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
