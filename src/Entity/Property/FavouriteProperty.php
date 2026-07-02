<?php

namespace App\Entity\Property;

use App\Entity\AbstractEntity;
use App\Entity\Security\User;
use App\Repository\Property\FavouritePropertyRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Table(name: 'favourite_properties')]
#[ORM\Index(name: 'index_id', columns: ['id'])]
#[ORM\UniqueConstraint(name: 'uniq_user_property', columns: ['user_id', 'property_id'])]
#[ORM\Entity(repositoryClass: FavouritePropertyRepository::class)]
class FavouriteProperty extends AbstractEntity
{
    #[ORM\Column(type: Types::GUID)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator('doctrine.uuid_generator')]
    #[Groups(['favourite:read'])]
    private ?string $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(targetEntity: Property::class, inversedBy: 'favouriteProperties')]
    #[ORM\JoinColumn(name: 'property_id', referencedColumnName: 'id', nullable: false)]
    private ?Property $property = null;

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

    public function getProperty(): ?Property
    {
        return $this->property;
    }

    public function setProperty(?Property $property): self
    {
        $this->property = $property;

        return $this;
    }
}
