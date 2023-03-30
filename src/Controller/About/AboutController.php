<?php

namespace App\Controller\About;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AboutController extends AbstractController
{
    #[Route('/about', name: 'about')]
    public function about(Request $request): Response
    {
        $userGender = $request->getSession()->get('gender');
        $userImage = $request->getSession()->get('userImage');
        return $this->render('about/about.html.twig',[
            'userGender' => $userGender,
            'userImage' => $userImage,
        ]);
    }
}