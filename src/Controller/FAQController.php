<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class FAQController extends AbstractController
{
    /**
     * @Route("/duk", name="app_faq")
     */
    public function index()
    {
        return $this->render('faq/index.html.twig', [
            'controller_name' => 'FAQController',
        ]);
    }
}
