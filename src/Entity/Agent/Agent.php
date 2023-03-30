<?php

namespace App\Entity\Agent;

use App\Entity\AbstractEntity;
use App\Repository\Agent\AgentRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'agent')]
#[ORM\Entity(repositoryClass: AgentRepository::class)]
class Agent extends AbstractEntity
{

    #[ORM\Column(nullable: true)]
    private ?string $agentNumber = null ;

    #[ORM\Column]
    private string $agentName;


    /**
     * @return string|null
     */
    public function getAgentNumber(): ?string
    {
        return $this->agentNumber;
    }

    /**
     * @param string|null $agentNumber
     */
    public function setAgentNumber(?string $agentNumber): void
    {
        $this->agentNumber = $agentNumber;
    }

    /**
     * @return string
     */
    public function getAgentName(): string
    {
        return $this->agentName;
    }

    /**
     * @param string $agentName
     */
    public function setAgentName(string $agentName): void
    {
        $this->agentName = $agentName;
    }

}
