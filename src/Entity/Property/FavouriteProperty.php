<?php

namespace App\Entity\Property;

use App\Entity\AbstractEntity;
use App\Repository\Property\FavouritePropertyRepository;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Types\Boolean;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Table(name: 'FavouriteProperties')]
#[ORM\Index(columns: ['id'], name: 'index_id')]
#[ORM\Entity(repositoryClass: FavouritePropertyRepository::class)]
class FavouriteProperty extends AbstractEntity
{
    #[ORM\Column(type: 'guid')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator('doctrine.uuid_generator')]
    #[Groups(['read'])]
    private ?string $id = null;

    #[ORM\Column(type: "string")]
    private string $ownerId;

    #[ORM\Column(type: "string")]
    private string $favourite;

    #[ORM\ManyToOne(inversedBy: 'favouriteProperties')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Property $property = null;


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
     * @return string
     */
    public function getFavourite(): string
    {
        return $this->favourite;
    }

    /**
     * @param string $favourite
     */
    public function setFavourite(string $favourite): void
    {
        $this->favourite = $favourite;
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
