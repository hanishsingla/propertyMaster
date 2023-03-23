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
    #[Route('/property-list', name: 'app_property_list')]
    public function propertyList(Request $request, PropertyRepository $propertyRepository): Response
    {
        $property = $propertyRepository->findAll();
        return $this->render('property/property.html.twig', [
            'site_meta_title_name' => 'properties',
        ]);
    }
    #[Route('/property-type', name: 'app_property_type')]
    public function propertyType(Request $request, PropertyRepository $propertyRepository): Response
    {
        return $this->render('property/property.html.twig', [
            'site_meta_title_name' => 'properties',
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