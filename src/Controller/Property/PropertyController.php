<?php

namespace App\Controller\Property;

use App\Entity\Property\FavouriteProperty;
use App\Repository\Property\FavouritePropertyRepository;
use App\Repository\Property\PropertyRepository;
use App\Service\CommonHelper;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
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
    public function propertyDetails(Request $request, PropertyRepository $propertyRepository, FavouritePropertyRepository $favoritePropertyRepository, $propertyId): Response
    {
        $ownerId = $request->getSession()->get('ownerId');

        $propertyInformation = $propertyRepository->getPropertyById($propertyId);
        return $this->render('property/detail.html.twig', [
            'propertyInformation' => $propertyInformation,
        ]);
    }

    #[Route('/property-search', name: 'propertySearch')]
    public function propertySearch(Request $request, PropertyRepository $propertyRepository): Response
    {
        $city = $request->get('city');
        $propertyType = $request->get('propertyType');
        $status = $request->get('status');

        $propertyList = $propertyRepository->getSearchProperty($city, $propertyType, $status);

        return $this->render('listing/property/property_listing.html.twig', [
            'site_meta_title_name' => 'properties',
            'propertyList' => $propertyList,
        ]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/favourite-property', name: 'favouriteProperty')]
    public function favouriteProperty(Request $request, FavouritePropertyRepository $favouritePropertyRepository): Response
    {
        $ownerId = $request->getSession()->get('ownerId');
        $favourites = $favouritePropertyRepository->getFavoritePropertyByFav($ownerId);
        return $this->render('base/favourite_property.html.twig', [
            'propertyLists' => $favourites
        ]);
    }

    #[Route('/liked-property/{propertyId}', name: 'likedProperty')]
    public function likedProperty(Request $request, CommonHelper $commonHelper, PropertyRepository $propertyRepository, EntityManagerInterface $em, $propertyId): Response
    {

        $ownerId = $request->getSession()->get('ownerId');
        $property = $propertyRepository->getPropertyById($propertyId, $ownerId);

        $data = $request->get('data');
        if (empty($property->getFavoriteProperty())) {
            $fav = new FavouriteProperty();
            $fav->setOwnerId($ownerId);
            $commonHelper->setCreatedDate($fav);
        } else {
            $fav = $property->getFavoriteProperty();
            $commonHelper->setUpdateDate($fav);
        }
        $fav->setFavourite($data);
        $property->setFavoriteProperty($fav);
        $em->persist($property);
        $em->flush();
        return $this->json('data');
    }

}