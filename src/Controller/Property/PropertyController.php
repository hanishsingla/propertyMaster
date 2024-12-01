<?php

namespace App\Controller\Property;

use App\Entity\Property\FavouriteProperty;
use App\Repository\Property\FavouritePropertyRepository;
use App\Repository\Property\PropertyRepository;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class PropertyController extends AbstractDashboardController
{
    #[Route('/property-list/{propertyType}', name: 'propertyList')]
    public function propertyList(Request $request, PropertyRepository $propertyRepository, ?string $propertyType = null): Response
    {
        $city = $request->get('city');
        $propertyCategory = $request->get('propertyCategory');
        $propertyType = $propertyType ?? $request->get('propertyType');
        $status = $request->get('status');

        if ($city && $propertyCategory && null != $status || $city && $propertyType && $propertyCategory && null != $status) {
            $propertyLists = $propertyRepository->getSearchProperty($city, $propertyCategory, $status, $propertyType);
        } else {
            $propertyLists = $propertyRepository->getAllProperty();
        }

        return $this->render('property/property.html.twig', [
            'propertyLists' => $propertyLists,
            'site_meta_title_name' => 'properties',
        ]);
    }

    #[Route('/property-details/{propertyId}', name: 'propertyDetails')]
    public function propertyDetails(Request $request, PropertyRepository $propertyRepository, FavouritePropertyRepository $favoritePropertyRepository, ?string $propertyId): Response
    {
        $this->getUser();

        $ownerId = $request->getSession()->get('ownerId');

        $propertyInformation = $propertyRepository->getProperty($propertyId);

        $fav = $favoritePropertyRepository->getFavoritePropertyById($propertyId, $ownerId);

        return $this->render('property/detail.html.twig', [
            'propertyInformation' => $propertyInformation,
            'fav' => $fav,
            'site_meta_title_name' => 'details',
        ]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/favourite-property', name: 'favouriteProperty')]
    public function favouriteProperty(Request $request, FavouritePropertyRepository $favouritePropertyRepository): Response
    {
        $user = $this->getUser();

        if (!$user instanceof UserInterface) {
            return $this->redirectToRoute('login');
        }

        $ownerId = $request->getSession()->get('ownerId');

        $favourites = $favouritePropertyRepository->getFavoritePropertyByFav($ownerId);

        return $this->render('property/favourite_property.html.twig', [
            'propertyLists' => $favourites,
            'site_meta_title_name' => 'favourite',
        ]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/liked-property/{propertyId}', name: 'likedProperty')]
    public function likedProperty(Request $request, FavouritePropertyRepository $favouritePropertyRepository, PropertyRepository $propertyRepository, EntityManagerInterface $em, ?string $propertyId): Response
    {
        $user = $this->getUser();

        if (!$user instanceof UserInterface) {
            return $this->redirectToRoute('login');
        }

        $ownerId = $request->getSession()->get('ownerId');

        $data = $request->get('data');

        $favourite = $favouritePropertyRepository->getFavoritePropertyById($propertyId, $ownerId);

        if (!$favourite instanceof FavouriteProperty) {
            $property = $propertyRepository->getProperty($propertyId);

            $favourite = (new FavouriteProperty())
                ->setProperty($property)
                ->setOwnerId($ownerId);

            $favourite->setIsCreatedAt(new \DateTime());
        } else {
            $favourite->setIsUpdatedAt(new \DateTime());
        }

        $favourite->setFavourite($data);

        $em->persist($favourite);

        $em->flush();

        return $this->json('data');
    }
}
