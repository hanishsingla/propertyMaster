<?php

namespace App\Controller;


use App\Form\Information\UserAddressType;
use App\Repository\Information\UserAddressRepository;
use App\Repository\Property\PropertyRepository;
use App\Service\Session\Session;
use App\Service\UploadHelper\UserImageUploader;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BaseController extends AbstractController
{
    #[Route('/{listType}', name: 'home', requirements: ['listType' => 'buy|rent|sale'])]
    public function home(Request $request, Session $session, PropertyRepository $propertyRepository, $listType = 'all'): Response
    {
        $user = $this->getUser();

        if($user){
            $session->session($user, $request);
        }

        $propertyLists = $propertyRepository->getPropertyByListType($listType);

        if ($request->isXmlHttpRequest()) {

            $propertyLists = $propertyRepository->getPropertyByListType($listType);

            return $this->render('listing/property/property_listing.html.twig', [
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
    public function account(Request $request, UserAddressRepository $addressRepository,UserImageUploader $imageUploader, EntityManagerInterface $em): Response
    {
        $ownerId = $request->getSession()->get('ownerId');
        $userAddress = $addressRepository->getUserAddress($ownerId);
        $form = $this->createForm(UserAddressType::class, $userAddress);
        $form->handleRequest($request);
        /** @var UploadedFile $brochureFile */
        $brochureFile = $form->get('image')->getData();

        if ($form->isSubmitted() && $form->isValid()) {

            if ($brochureFile) {

                $brochureFileName = $imageUploader->upload($brochureFile);

                $userAddress->setImage($brochureFileName);
            }
            $em->persist($userAddress);
            $em->flush();

            return $this->redirectToRoute('account');
        }
        return $this->render('base/user_profile.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}