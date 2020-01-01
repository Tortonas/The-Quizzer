<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    /**
     * @Route("/profilis", name="app_profile")
     * @IsGranted("ROLE_USER")
     */
    public function index()
    {
        return $this->render('profile/index.html.twig', [
            'user' => $this->getUser(),
        ]);
    }

    /**
     * @Route("/profilis/keisti", name="app_profile_edit")
     * @IsGranted("ROLE_USER")
     * @param Request $request
     * @return Response
     */
    public function edit(Request $request)
    {
        if($request->get('v') == 'p')
        {
            return $this->render('profile/changePass.html.twig', [
                'user' => $this->getUser(),
            ]);
        }
        else if($request->get('v') == 'e')
        {
            return $this->render('profile/changeEmail.html.twig', [
                'user' => $this->getUser(),
            ]);
        }
    }
}
