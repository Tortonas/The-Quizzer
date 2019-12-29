<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    /**
     * @Route("/profilis", name="app_profile")
     */
    public function index()
    {
        return $this->render('profile/index.html.twig', [
            'controller_name' => 'ProfileController',
        ]);
    }

    /**
     * @Route("/profilis/keisti", name="app_profile_edit")
     */
    public function edit()
    {
        return $this->render('profile/change.html.twig', [
            'controller_name' => 'ProfileController',
        ]);
    }
}
