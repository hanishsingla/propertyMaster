<?php

namespace App\Entity;

use App\Repository\AgentRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: AgentRepository::class)]
class Agent extends AbstractEntity
{
    #[ORM\Column(type: 'guid')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator('doctrine.uuid_generator')]
    #[Groups(['read'])]
    private ?string $id = null;

    #[ORM\Column(nullable: true)]
    private ?string $agentNumber = null ;

    #[ORM\Column]
    private string $agentName;


    public function getId(): ?string
    {
        return $this->id;
    }

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
