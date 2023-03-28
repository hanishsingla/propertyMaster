<?php

namespace App\Controller;


use App\Repository\PropertyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BaseController extends AbstractController
{
    #[Route('/{listType}', name: 'home' , requirements: ['listType' => 'buy|rent|sale'])]
    public function home(Request $request, PropertyRepository $propertyRepository, $listType = 'all'): Response
    {

        $propertyLists = $propertyRepository->getPropertyByListType($listType);

        if($request->isXmlHttpRequest()){

            $propertyLists = $propertyRepository->getPropertyByListType($listType);

            return $this->render('listing/home/home_listing.html.twig',[
                'propertyLists' => $propertyLists,
            ]);
        }

        return $this->render('base/home.html.twig', [
            'propertyLists' => $propertyLists,
            'listType' => $listType,
        ]);
    }
}