<?php

namespace App\Entity\Property;

use App\Entity\AbstractEntity;
use App\Repository\Property\FavoritePropertyRepository;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Types\Boolean;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Table(name: 'FavoriteProperty')]
#[ORM\Index(columns: ['id'], name: 'index_id')]
#[ORM\Entity(repositoryClass: FavoritePropertyRepository::class)]
class FavoriteProperty extends AbstractEntity
{
    #[ORM\Column(type: 'guid')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator('doctrine.uuid_generator')]
    #[Groups(['read'])]
    private ?string $id = null;

    #[ORM\Column(type: "string")]
    private string $ownerId;

    #[ORM\Column(type: 'boolean' ,options: ['default' => 0])]
    private bool $favourite = false;

    #[ORM\OneToOne(inversedBy: 'favoriteProperty', cascade: ['persist', 'remove'])]
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
     * @return bool
     */
    public function isFavourite(): bool
    {
        return $this->favourite;
    }

    /**
     * @param bool $favourite
     */
    public function setFavourite(bool $favourite): void
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
