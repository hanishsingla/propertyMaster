<?php

namespace App\Controller\Api;

use App\Api\ApiController;
use App\Api\Presenter\PropertyPresenter;
use App\Repository\Property\PropertyRepository;
use App\Security\Voter\PropertyVoter;
use App\Service\PropertyImageService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/properties/{id}/images')]
class PropertyImageApiController extends ApiController
{
    public function __construct(
        private readonly PropertyRepository $properties,
        private readonly PropertyImageService $imageService,
        private readonly PropertyPresenter $presenter,
    ) {
    }

    #[Route('', name: 'api_property_image_add', methods: ['POST'])]
    public function add(string $id, Request $request): JsonResponse
    {
        $property = $this->load($id);
        $this->denyAccessUnlessGranted(PropertyVoter::EDIT, $property);

        $uploaded = $request->files->all();
        $files = $uploaded['images'] ?? array_values($uploaded);
        if (!is_array($files)) {
            $files = [$files];
        }

        $property = $this->imageService->addImages($property, array_filter($files));

        return new JsonResponse($this->presenter->detail($property, []));
    }

    #[Route('', name: 'api_property_image_reorder', methods: ['PATCH'])]
    public function reorder(string $id, Request $request): JsonResponse
    {
        $property = $this->load($id);
        $this->denyAccessUnlessGranted(PropertyVoter::EDIT, $property);

        $ops = $this->decode($request);
        $property = $this->imageService->reorder($property, array_values($ops));

        return new JsonResponse($this->presenter->detail($property, []));
    }

    #[Route('/{imageId}', name: 'api_property_image_delete', methods: ['DELETE'])]
    public function delete(string $id, string $imageId): JsonResponse
    {
        $property = $this->load($id);
        $this->denyAccessUnlessGranted(PropertyVoter::EDIT, $property);
        $this->imageService->deleteImage($property, $imageId);

        return $this->noContent();
    }

    private function load(string $id): \App\Entity\Property\Property
    {
        $property = $this->properties->findActiveById($id);
        if (null === $property) {
            throw new NotFoundHttpException('Property not found.');
        }

        return $property;
    }
}
