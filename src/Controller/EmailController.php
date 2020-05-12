<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EmailController extends AbstractController
{
    /**
     * @Route("/test_email", name="emails")
     * @param \Swift_Mailer $mailer
     * @return Response
     */
    public function index(\Swift_Mailer $mailer)
    {
        $message = (new \Swift_Message('Hello Email'))
            ->setFrom('quizzerlt@gmail.com')
            ->setTo('plankton546@gmail.com')
            ->setBody(
                $this->renderView(
                    'emails/registration.html.twig',
                    ['name' => 'Testuotojas']
                ),
                'text/html'
            );

        $mailer->send($message);


        return $this->render('emails/index.html.twig', [
            'controller_name' => 'EmailController',
        ]);
    }
}
