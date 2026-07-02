<?php

namespace App\Entity\Account;

use App\Entity\AbstractEntity;
use App\Entity\Security\User;
use App\Repository\Account\ContactRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: ContactRepository::class)]
#[ORM\Table(name: 'helpdesk_tickets')]
#[ORM\Index(name: 'index_id', columns: ['id'])]
class Contact extends AbstractEntity
{
    #[ORM\Column(type: Types::GUID)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator('doctrine.uuid_generator')]
    #[Groups(['contact:read'])]
    private ?string $id = null;

    /** Set when a logged-in user submits the form; null for anonymous. */
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: true)]
    private ?User $user = null;

    #[ORM\Column(length: 255)]
    #[Groups(['contact:read'])]
    private string $name;

    #[ORM\Column(length: 180)]
    #[Groups(['contact:read'])]
    private string $email;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['contact:read'])]
    private string $message;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }
}
