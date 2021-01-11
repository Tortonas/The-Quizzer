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
        if ($this->isGranted('ROLE_ADMIN')) {
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
        if ($this->isGranted('ROLE_ADMIN')) {
            yield MenuItem::linktoDashboard('Dashboard', 'fa fa-home');
            yield MenuItem::linkToCrud('Questions', 'fas fa-question', Question::class);
            yield MenuItem::linkToCrud('Question answers', 'fab fa-acquisitions-incorporated', QuestionAnswer::class);
            yield MenuItem::linkToCrud('Discord users', 'fab fa-discord', DiscordUser::class);
            yield MenuItem::linkToCrud('Users', 'fas fa-users', User::class);
            yield MenuItem::linkToCrud('FAQ\'s', 'far fa-question-circle', FreqAskedQuestion::class);
            yield MenuItem::linkToCrud('Global notifications', 'fas fa-bell', GlobalNotification::class);
        }
    }
}
