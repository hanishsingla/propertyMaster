<?php

namespace App\Controller\Api;

use App\Api\ApiController;
use App\Api\Presenter\PropertyPresenter;
use App\Entity\Property\Property;
use App\Entity\Security\User;
use App\Repository\Property\FavouritePropertyRepository;
use App\Repository\Property\PropertyRepository;
use App\Service\FavouriteService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/favourites')]
#[IsGranted('ROLE_USER')]
class FavouriteApiController extends ApiController
{
    public function __construct(
        private readonly FavouritePropertyRepository $favourites,
        private readonly PropertyRepository $properties,
        private readonly FavouriteService $favouriteService,
        private readonly PropertyPresenter $presenter,
    ) {
    }

    #[Route('', name: 'api_favourite_list', methods: ['GET'])]
    public function list(#[CurrentUser] User $user): JsonResponse
    {
        $props = $this->favourites->findFavouritedProperties($user);
        $favSet = [];
        foreach ($props as $p) {
            $favSet[$p->getId()] = true;
        }
        $data = array_map(fn (Property $p) => $this->presenter->list($p, $favSet), $props);

        return new JsonResponse(['data' => $data]);
    }

    #[Route('/{propertyId}', name: 'api_favourite_add', methods: ['POST'])]
    public function add(string $propertyId, #[CurrentUser] User $user): JsonResponse
    {
        $this->favouriteService->add($user, $this->load($propertyId));

        return new JsonResponse(['favourited' => true]);
    }

    #[Route('/{propertyId}', name: 'api_favourite_remove', methods: ['DELETE'])]
    public function remove(string $propertyId, #[CurrentUser] User $user): JsonResponse
    {
        $this->favouriteService->remove($user, $this->load($propertyId));

        return $this->noContent();
    }

    private function load(string $id): Property
    {
        $property = $this->properties->findActiveById($id);
        if (null === $property) {
            throw new NotFoundHttpException('Property not found.');
        }

        return $property;
    }
}
