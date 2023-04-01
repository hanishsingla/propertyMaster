<?php

namespace App\Controller;


use App\Form\Information\UserInformationType;
use App\Repository\Information\UserInformationRepository;
use App\Repository\Property\PropertyRepository;
use App\Service\Session\Session;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BaseController extends AbstractController
{
    #[Route('/{listType}', name: 'home', requirements: ['listType' => 'buy|rent|sale'])]
    public function home(Request $request, Session $session, PropertyRepository $propertyRepository, $listType = 'all'): Response
    {
        $user = $this->getUser();
        $session->session($user, $request);
        $propertyLists = $propertyRepository->getPropertyByListType($listType);

        if ($request->isXmlHttpRequest()) {

            $propertyLists = $propertyRepository->getPropertyByListType($listType);

            return $this->render('listing/home/home_listing.html.twig', [
                'propertyLists' => $propertyLists,
            ]);
        }

        return $this->render('base/home.html.twig', [
            'propertyLists' => $propertyLists,
            'listType' => $listType,
        ]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/account', name: 'account')]
    public function account(Request $request, UserInformationRepository $informationRepository, EntityManagerInterface $em): Response
    {
        $ownerId = $request->getSession()->get('ownerId');
        $userInformation = $informationRepository->getUserInformation($ownerId);
        $form = $this->createForm(UserInformationType::class, $userInformation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($userInformation);
            $em->flush();
            return $this->redirectToRoute('account');
        }
        return $this->render('base/user_profile.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}