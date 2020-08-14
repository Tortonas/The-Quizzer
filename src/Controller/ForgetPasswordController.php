<?php

namespace App\Controller;

use App\Form\RemindPasswordFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ForgetPasswordController extends AbstractController
{
    /**
     * @Route("/forget/password", name="forget_password")
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $remindPasswordForm = $this->createForm(RemindPasswordFormType::class);

        $remindPasswordForm->handleRequest($request);

        $formSubmitted = false;

        if ($remindPasswordForm->isSubmitted() && $remindPasswordForm->isValid()) {
            $formSubmitted = true;
            // TODO dabaigt issiuntimo forma
        }

        return $this->render('forget_password/index.html.twig', [
            'form' => $remindPasswordForm->createView(),
            'formSubmitted' => $formSubmitted
        ]);
    }
}
