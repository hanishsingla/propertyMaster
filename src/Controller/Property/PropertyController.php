<?php

namespace App\Controller\Property;

use App\Repository\Property\PropertyRepository;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class PropertyController extends AbstractDashboardController
{
    #[Route('/property-list', name: 'propertyList')]
    public function propertyList(Request $request, PropertyRepository $propertyRepository): Response
    {
        return $this->render('property/property.html.twig', [
            'site_meta_title_name' => 'properties',
        ]);
    }

    #[Route('/property-type', name: 'propertyType')]
    public function propertyType(Request $request, PropertyRepository $propertyRepository): Response
    {
        return $this->render('property/property_type.html.twig', [
            'site_meta_title_name' => 'properties',
        ]);
    }

    #[Route('/property-agent', name: 'propertyAgent')]
    public function propertyAgent(Request $request, PropertyRepository $propertyRepository): Response
    {
        return $this->render('property/property_agent.html.twig', [
            'site_meta_title_name' => 'properties',
        ]);
    }
    #[Route('/property-details/{propertyId}', name: 'propertyDetails')]
    public function propertyDetails(Request $request, PropertyRepository $propertyRepository,$propertyId): Response
    {
        $information = $propertyRepository->getProperty($propertyId);

        return $this->render('property/detail.html.twig');
    }
    #[Route('/property-search', name: 'propertySearch')]
    public function propertySearch(Request $request, PropertyRepository $propertyRepository): Response
    {
        $city = $request->get('city');
        $propertyType = $request->get('propertyType');
        $status = $request->get('status');

        $propertyList = $propertyRepository->getSearchProperty($city,$propertyType,$status);

        return $this->render('listing/property/property_listing.html.twig', [
            'site_meta_title_name' => 'properties',
            'propertyList' => $propertyList,
        ]);
    }
}