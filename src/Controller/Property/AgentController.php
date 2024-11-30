<?php

namespace App\Controller\Property;

use App\Repository\Property\PropertyRepository;
use App\Repository\Security\UserDetailRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AgentController extends AbstractController
{
    #[Route('/property-agents', name: 'propertyAgents')]
    public function propertyAgents(Request $request, PropertyRepository $propertyRepository, UserDetailRepository $detailRepository): Response
    {
        $agents = $detailRepository->getAgents();

        return $this->render('property/property_agent.html.twig', [
            'agents' => $agents,
            'site_meta_title_name' => 'agent',
        ]);
    }
}
