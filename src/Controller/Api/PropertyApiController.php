<?php

namespace App\Controller\Api;

use App\Api\ApiController;
use App\Api\Presenter\PropertyPresenter;
use App\Entity\Property\Property;
use App\Entity\Security\User;
use App\Enum\PropertyStatus;
use App\Repository\Property\FavouritePropertyRepository;
use App\Repository\Property\PropertyRepository;
use App\Security\Voter\PropertyVoter;
use App\Service\PropertyService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api')]
class PropertyApiController extends ApiController
{
    public function __construct(
        private readonly PropertyRepository $properties,
        private readonly PropertyPresenter $presenter,
        private readonly FavouritePropertyRepository $favourites,
        private readonly PropertyService $propertyService,
    ) {
    }

    #[Route('/properties', name: 'api_property_list', methods: ['GET'])]
    public function list(Request $request, #[CurrentUser] ?User $user): JsonResponse
    {
        [$page, $perPage] = $this->paging($request);
        $filters = $this->propertyService->buildFilters($request->query->all());
        // Public listing is always restricted to published.
        $result = $this->properties->search($filters, $page, $perPage);

        $favSet = $this->favSet($user, $result['items']);
        $data = array_map(fn (Property $p) => $this->presenter->list($p, $favSet), $result['items']);

        return $this->envelope($data, $page, $perPage, $result['total']);
    }

    #[Route('/my/properties', name: 'api_property_mine', methods: ['GET'])]
    #[IsGranted('ROLE_AGENT')]
    public function mine(Request $request, #[CurrentUser] User $user): JsonResponse
    {
        [$page, $perPage] = $this->paging($request);
        $filters = $this->propertyService->buildFilters($request->query->all());
        $filters['owner'] = $user;

        // Owner sees every status; allow narrowing to one explicit status.
        $status = $request->query->get('status');
        if (null !== $status && $resolved = PropertyStatus::tryFrom((string) $status)) {
            $filters['status'] = $resolved;
        } else {
            $filters['allStatuses'] = true;
        }

        $result = $this->properties->search($filters, $page, $perPage);
        $data = array_map(fn (Property $p) => $this->presenter->list($p, []), $result['items']);

        return $this->envelope($data, $page, $perPage, $result['total']);
    }

    #[Route('/properties/{idOrSlug}', name: 'api_property_detail', methods: ['GET'])]
    public function detail(string $idOrSlug, #[CurrentUser] ?User $user): JsonResponse
    {
        $property = $this->properties->findActiveByIdOrSlug($idOrSlug);
        if (null === $property) {
            throw new NotFoundHttpException('Property not found.');
        }

        // Hide non-published from everyone except the owner/admin.
        if (PropertyStatus::Published !== $property->getStatus() && !$this->canManage($property, $user)) {
            throw new NotFoundHttpException('Property not found.');
        }

        $favSet = $this->favSet($user, [$property]);

        return new JsonResponse($this->presenter->detail($property, $favSet));
    }

    #[Route('/properties', name: 'api_property_create', methods: ['POST'])]
    #[IsGranted('ROLE_AGENT')]
    public function create(Request $request, #[CurrentUser] User $user): JsonResponse
    {
        $this->assertVerified($user);
        $property = $this->propertyService->create($this->decode($request), $user);

        return new JsonResponse($this->presenter->detail($property, []), Response::HTTP_CREATED);
    }

    #[Route('/properties/{id}', name: 'api_property_update', methods: ['PATCH'])]
    public function update(string $id, Request $request): JsonResponse
    {
        $property = $this->loadOr404($id);
        $this->denyAccessUnlessGranted(PropertyVoter::EDIT, $property);
        $property = $this->propertyService->update($property, $this->decode($request));

        return new JsonResponse($this->presenter->detail($property, []));
    }

    #[Route('/properties/{id}', name: 'api_property_delete', methods: ['DELETE'])]
    public function delete(string $id): JsonResponse
    {
        $property = $this->loadOr404($id);
        $this->denyAccessUnlessGranted(PropertyVoter::DELETE, $property);
        $this->propertyService->softDelete($property);

        return $this->noContent();
    }

    private function loadOr404(string $id): Property
    {
        $property = $this->properties->findActiveById($id);
        if (null === $property) {
            throw new NotFoundHttpException('Property not found.');
        }

        return $property;
    }

    private function canManage(Property $property, ?User $user): bool
    {
        return null !== $user && $this->isGranted(PropertyVoter::EDIT, $property);
    }

    private function assertVerified(User $user): void
    {
        if (!$user->isVerified()) {
            throw new AccessDeniedHttpException('Please verify your email before creating listings.');
        }
    }

    /**
     * @param Property[] $items
     *
     * @return array<string,bool>
     */
    private function favSet(?User $user, array $items): array
    {
        if (null === $user || [] === $items) {
            return [];
        }
        $ids = array_map(fn (Property $p) => $p->getId(), $items);

        return $this->favourites->favouritedIdSet($user, $ids);
    }

    /**
     * @return array{0:int,1:int}
     */
    private function paging(Request $request): array
    {
        $page = max(1, (int) $request->query->get('page', 1));
        $perPage = (int) $request->query->get('perPage', 12);
        $perPage = min(50, max(1, $perPage));

        return [$page, $perPage];
    }
}
