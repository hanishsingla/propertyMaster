<?php

namespace App\Controller\About;

use App\Service\CommonHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AboutController extends AbstractController
{
    #[Route('/about', name: 'about')]
    public function about(Request $request ,CommonHelper $commonHelper): Response
    {

        return $this->render('about/about.html.twig',[

        ]);
    }
}