<?php

namespace App\Controller\Api;

use App\Api\ApiController;
use App\Api\Presenter\PropertyPresenter;
use App\Api\Presenter\UserPresenter;
use App\Entity\Property\Property;
use App\Entity\Security\User;
use App\Repository\Property\PropertyRepository;
use App\Repository\Security\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/agents')]
class AgentApiController extends ApiController
{
    public function __construct(
        private readonly UserRepository $users,
        private readonly PropertyRepository $properties,
        private readonly UserPresenter $userPresenter,
        private readonly PropertyPresenter $propertyPresenter,
    ) {
    }

    #[Route('', name: 'api_agent_list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $page = max(1, (int) $request->query->get('page', 1));
        $perPage = min(50, max(1, (int) $request->query->get('perPage', 12)));
        $result = $this->users->searchAgents($page, $perPage);

        $data = array_map(fn (User $u) => $this->userPresenter->agent($u), $result['items']);

        return $this->envelope($data, $page, $perPage, $result['total']);
    }

    #[Route('/{id}', name: 'api_agent_detail', methods: ['GET'])]
    public function detail(string $id): JsonResponse
    {
        $agent = $this->users->findAgent($id);
        if (null === $agent) {
            throw new NotFoundHttpException('Agent not found.');
        }

        $listings = $this->properties->search(['owner' => $agent], 1, 50);
        $data = $this->userPresenter->agent($agent);
        $data['properties'] = array_map(
            fn (Property $p) => $this->propertyPresenter->list($p, []),
            $listings['items']
        );

        return new JsonResponse($data);
    }
}
