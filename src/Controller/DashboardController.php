<?php

namespace App\Controller;

use App\Controller\Crud\QuestionCrudController;
use App\Entity\DiscordUser;
use App\Entity\FreqAskedQuestion;
use App\Entity\GlobalNotification;
use App\Entity\Question;
use App\Entity\QuestionAnswer;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\CrudUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        if ($this->getUser() && $this->getUser()->getRoles()[0] == 'ROLE_ADMIN') {
            $routeBuilder = $this->get(CrudUrlGenerator::class)->build();

            return $this->redirect($routeBuilder->setController(QuestionCrudController::class)->generateUrl());
        }

        return $this->redirectToRoute('app_login');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Admin panel');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linktoDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Questions', 'icon class', Question::class);
        yield MenuItem::linkToCrud('Question answers', 'icon class', QuestionAnswer::class);
        yield MenuItem::linkToCrud('Discord users', 'icon class', DiscordUser::class);
        yield MenuItem::linkToCrud('Users', 'icon class', User::class);
        yield MenuItem::linkToCrud('FAQ\'s', 'icon class', FreqAskedQuestion::class);
        yield MenuItem::linkToCrud('Global notifications', 'icon class', GlobalNotification::class);

    }
}
