<?php

namespace App\Controller;

use App\Entity\Account\Contact;
use App\Form\Account\ContactType;
use App\Repository\Property\PropertyRepository;
use App\Service\Session\Session;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class BaseController extends AbstractController
{
    #[Route('/{listType}', name: 'home', requirements: ['listType' => 'buy|rent|sale'])]
    public function home(Request $request, Session $session, PropertyRepository $propertyRepository, $listType = 'all'): Response
    {
        $propertyLists = $propertyRepository->getPropertyByListType($listType, '4');

        if ($request->isXmlHttpRequest()) {
            $propertyLists = $propertyRepository->getPropertyByListType($listType, 4);

            return $this->render('listing/property/property_listing.html.twig', [
                'propertyLists' => $propertyLists,
            ]);
        }

        return $this->render('base/home.html.twig', [
            'propertyLists' => $propertyLists,
            'listType' => $listType,
        ]);
    }

    #[Route('/about', name: 'about')]
    public function about(): Response
    {
        return $this->render('about/about.html.twig');
    }

    #[Route('/contact', name: 'contact')]
    public function contact(Request $request, EntityManagerInterface $em): Response
    {
        $helpData = new Contact();
        $form = $this->createForm(ContactType::class, $helpData);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($helpData);

            $em->flush();

            return $this->redirectToRoute('contact');
        }

        return $this->render('contact/contact.html.twig', [
            'helpData' => $form->createView(),
        ]);
    }
}
