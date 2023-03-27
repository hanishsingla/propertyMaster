<?php

namespace App\Controller;


use App\Repository\PropertyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BaseController extends AbstractController
{
    #[Route('//{listType}', name: 'home' , requirements: ['listType' => 'buy|rent|sale'])]
    public function home(Request $request, PropertyRepository $propertyRepository, $listType = 'all'): Response
    {
        if ($listType == 'rent') {
            $propertyLists = $propertyRepository->findBy(['propertyStatus' => 'rent']);
        } elseif ($listType == 'sale') {
            $propertyLists = $propertyRepository->findBy(['propertyStatus' => 'sale']);
        } elseif ($listType == 'buy') {
            $propertyLists = $propertyRepository->findBy(['propertyStatus' => 'buy']);
        } else {
            $propertyLists = $propertyRepository->findBy(['propertyStatus' => ['buy', 'rent', 'sale']]);
        }

        return $this->render('base/home.html.twig', [
            'propertyLists' => $propertyLists,
            'listType' => $listType,
        ]);
    }
}