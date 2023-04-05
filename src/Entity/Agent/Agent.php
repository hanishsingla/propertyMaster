<?php

namespace App\Entity\Agent;

use App\Entity\AbstractEntity;
use App\Repository\Agent\AgentRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Table(name: 'agent')]
#[ORM\Entity(repositoryClass: AgentRepository::class)]
class Agent extends AbstractEntity
{
    #[ORM\Column(type: 'guid')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator('doctrine.uuid_generator')]
    #[Groups(['read'])]
    private ?string $id = null;

    #[ORM\Column(type: "string")]
    private string $ownerId;

    #[ORM\Column(nullable: true)]
    private ?string $agentNumber = null ;

    #[ORM\Column]
    private string $agentName;

    /**
     * @return string|null
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getOwnerId(): string
    {
        return $this->ownerId;
    }

    /**
     * @param string $ownerId
     */
    public function setOwnerId(string $ownerId): void
    {
        $this->ownerId = $ownerId;
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
