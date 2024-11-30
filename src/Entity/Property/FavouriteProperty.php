<?php

namespace App\Entity\Property;

use App\Entity\AbstractEntity;
use App\Repository\Property\FavouritePropertyRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Table(name: 'favourite_properties')]
#[ORM\Index(name: 'index_id', columns: ['id'])]
#[ORM\Entity(repositoryClass: FavouritePropertyRepository::class)]
class FavouriteProperty extends AbstractEntity
{
    #[ORM\Column(type: 'guid')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator('doctrine.uuid_generator')]
    #[Groups(['read'])]
    private ?string $id = null;

    #[ORM\Column(type: 'string')]
    private string $ownerId;

    #[ORM\Column(type: 'string')]
    private string $favourite;

    #[ORM\ManyToOne(inversedBy: 'favouriteProperties')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Property $property = null;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getOwnerId(): string
    {
        return $this->ownerId;
    }

    public function setOwnerId(string $ownerId): self
    {
        $this->ownerId = $ownerId;

        return $this;
    }

    public function getFavourite(): string
    {
        return $this->favourite;
    }

    public function setFavourite(string $favourite): self
    {
        $this->favourite = $favourite;

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
