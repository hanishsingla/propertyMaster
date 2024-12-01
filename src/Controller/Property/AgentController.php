<?php

namespace App\Controller\Property;

use App\Repository\Property\PropertyRepository;
use App\Repository\Security\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AgentController extends AbstractController
{
    #[Route('/property-agents', name: 'propertyAgents')]
    public function propertyAgents(UserRepository $detailRepository): Response
    {
        $agents = $detailRepository->getAgents();

        return $this->render('property/property_agent.html.twig', [
            'agents' => $agents,
            'site_meta_title_name' => 'agent',
        ]);
    }
}
