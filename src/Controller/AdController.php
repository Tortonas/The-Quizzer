<?php

namespace App\Controller;

use App\Entity\Question;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AdController extends AbstractController
{
    /**
     * @Route("/ad", name="ad")
     */
    public function index()
    {
        $currentQuestion = $this->getDoctrine()->getManager()->getRepository(Question::class)->findOneBy(array(
            'active' => 1));

        return $this->render('ad/index.html.twig', [
            'currentQuestion' => $currentQuestion
        ]);
    }
}
