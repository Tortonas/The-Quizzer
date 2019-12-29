<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ResultsController extends AbstractController
{
    /**
     * @Route("/rezultatai", name="app_results")
     */
    public function index()
    {
        return $this->render('results/index.html.twig', [
            'controller_name' => 'ResultsController',
        ]);
    }
}
