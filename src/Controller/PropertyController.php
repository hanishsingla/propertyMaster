<?php

namespace App\Controller;

use App\Form\PropertyType;
use App\Repository\AgentRepository;
use App\Repository\PropertyRepository;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class PropertyController extends AbstractDashboardController
{
    #[IsGranted('ROLE_USER')]
    #[Route('/property', name: 'app_property')]
    public function property(Request $request, AgentRepository $agentRepository, PropertyRepository $propertyRepository, $propertyType = 'dashboard'): Response
    {
        return $this->render('property/property.html.twig', [
            'propertyType' => $propertyType,
        ]);
    }


    #[Route('/propertyEdit/{id}', name: 'propertyEdit')]
    public function admin(Request $request, EntityManagerInterface $em, PropertyRepository $propertyRepository, $id): Response
    {
        $propertyData = $propertyRepository->findOneBy(['id' => $id]);
        $form = $this->createForm(PropertyType::class, $propertyData);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->persist($propertyData);

            $em->flush();

            return $this->redirectToRoute('app_property');
        }

        return $this->render('property/edit_property.html.twig', [
            'propertyForm' => $form->createView(),

        ]);
    }

    #[Route('/propertyDelete/{id}', name: 'app_delete')]
    public function delete(Request $request, EntityManagerInterface $em, PropertyRepository $propertyRepository, AgentRepository $agentRepository, $type, $id): Response
    {
        $property = $propertyRepository->findOneBy(['id' => $id]);
        $em->remove($property);

        $em->flush();
        return $this->redirectToRoute('app_property', ['type' => $type]);
    }
}