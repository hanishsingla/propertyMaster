<?php

namespace App\Controller;

use App\Form\SearchType;
use App\Repository\PropertyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BaseController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function home(Request $request, PropertyRepository $propertyRepository): Response
    {
        $queryBuilder = $propertyRepository->createQueryBuilder('p');


       $properties =  $propertyRepository->findBy(['propertyStatus' => ['rent', 'sale']]);


        return $this->render('base/home.html.twig', [
           'properties' => $properties,
        ]);
    }
}