<?php

namespace App\Entity\Property;

use App\Entity\AbstractEntity;
use App\Repository\Property\PropertyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Table(name: 'properties')]
#[ORM\Index(columns: ['id'], name: 'index_id')]
#[ORM\Entity(repositoryClass: PropertyRepository::class)]
class Property extends AbstractEntity
{
    #[ORM\Column(type: 'guid')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator('doctrine.uuid_generator')]
    #[Groups(['read'])]
    private ?string $id = null;

    #[ORM\Column(type: 'string')]
    private string $ownerId;

    #[ORM\Column(type: 'boolean', options: ['default' => 0])]
    private bool $propertyIsGarage = false;

    #[ORM\Column]
    private string $propertyArea;

    #[ORM\Column]
    private string $propertyBathRooms;

    #[ORM\Column]
    private string $propertyCategory;

    #[ORM\Column]
    private string $propertyCity;

    #[ORM\Column]
    private string $propertyCountry = 'india';

    #[ORM\Column(type: 'string', length: '1000')]
    private string $propertyDescription;

    #[ORM\Column]
    private string $propertyDirection;

    #[ORM\Column(nullable: true)]
    private ?string $propertyGarage = null;

    #[ORM\Column(nullable: true)]
    private ?array $propertyImage = null;

    #[ORM\Column]
    private string $propertyPrice;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $propertyRooms = null;

    #[ORM\Column]
    private string $propertyState;

    #[ORM\Column]
    private string $propertyStatus;

    #[ORM\Column]
    private string $propertyTitle;

    #[ORM\Column]
    private string $propertyType;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $propertyBedRooms = null;

    #[ORM\Column]
    private string $squareType;

    #[ORM\OneToMany(mappedBy: 'property', targetEntity: FavouriteProperty::class, cascade: ['persist', 'remove'])]
    private Collection $favouriteProperties;

    public function __construct()
    {
        $this->favouriteProperties = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getOwnerId(): string
    {
        return $this->ownerId;
    }

    public function setOwnerId(string $ownerId): void
    {
        $this->ownerId = $ownerId;
    }

    public function isPropertyIsGarage(): bool
    {
        return $this->propertyIsGarage;
    }

    public function setPropertyIsGarage(bool $propertyIsGarage): void
    {
        $this->propertyIsGarage = $propertyIsGarage;
    }

    public function getPropertyArea(): string
    {
        return $this->propertyArea;
    }

    public function setPropertyArea(string $propertyArea): void
    {
        $this->propertyArea = $propertyArea;
    }

    public function getPropertyBathRooms(): string
    {
        return $this->propertyBathRooms;
    }

    public function setPropertyBathRooms(string $propertyBathRooms): void
    {
        $this->propertyBathRooms = $propertyBathRooms;
    }

    public function getPropertyCategory(): string
    {
        return $this->propertyCategory;
    }

    public function setPropertyCategory(string $propertyCategory): void
    {
        $this->propertyCategory = $propertyCategory;
    }

    public function getPropertyCity(): string
    {
        return $this->propertyCity;
    }

    public function setPropertyCity(string $propertyCity): void
    {
        $this->propertyCity = $propertyCity;
    }

    public function getPropertyCountry(): string
    {
        return $this->propertyCountry;
    }

    public function setPropertyCountry(string $propertyCountry): void
    {
        $this->propertyCountry = $propertyCountry;
    }

    public function getPropertyDescription(): string
    {
        return $this->propertyDescription;
    }

    public function setPropertyDescription(string $propertyDescription): void
    {
        $this->propertyDescription = $propertyDescription;
    }

    public function getPropertyDirection(): string
    {
        return $this->propertyDirection;
    }

    public function setPropertyDirection(string $propertyDirection): void
    {
        $this->propertyDirection = $propertyDirection;
    }

    public function getPropertyGarage(): ?string
    {
        return $this->propertyGarage;
    }

    public function setPropertyGarage(?string $propertyGarage): void
    {
        $this->propertyGarage = $propertyGarage;
    }

    public function getPropertyImage(): ?array
    {
        return $this->propertyImage;
    }

    public function setPropertyImage(?array $propertyImage): void
    {
        $this->propertyImage = $propertyImage;
    }

    public function getPropertyPrice(): string
    {
        return $this->propertyPrice;
    }

    public function setPropertyPrice(string $propertyPrice): void
    {
        $this->propertyPrice = $propertyPrice;
    }

    public function getPropertyRooms(): ?string
    {
        return $this->propertyRooms;
    }

    public function setPropertyRooms(?string $propertyRooms): void
    {
        $this->propertyRooms = $propertyRooms;
    }

    public function getPropertyState(): string
    {
        return $this->propertyState;
    }

    public function setPropertyState(string $propertyState): void
    {
        $this->propertyState = $propertyState;
    }

    public function getPropertyStatus(): string
    {
        return $this->propertyStatus;
    }

    public function setPropertyStatus(string $propertyStatus): void
    {
        $this->propertyStatus = $propertyStatus;
    }

    public function getPropertyTitle(): string
    {
        return $this->propertyTitle;
    }

    public function setPropertyTitle(string $propertyTitle): void
    {
        $this->propertyTitle = $propertyTitle;
    }

    public function getPropertyType(): string
    {
        return $this->propertyType;
    }

    public function setPropertyType(string $propertyType): void
    {
        $this->propertyType = $propertyType;
    }

    public function getPropertyBedRooms(): ?string
    {
        return $this->propertyBedRooms;
    }

    public function setPropertyBedRooms(?string $propertyBedRooms): void
    {
        $this->propertyBedRooms = $propertyBedRooms;
    }

    public function getSquareType(): string
    {
        return $this->squareType;
    }

    public function setSquareType(string $squareType): void
    {
        $this->squareType = $squareType;
    }

    /**
     * @return Collection<int, FavouriteProperty>
     */
    public function getFavouriteProperties(): Collection
    {
        return $this->favouriteProperties;
    }

    public function addFavouriteProperty(FavouriteProperty $favouriteProperty): self
    {
        if (!$this->favouriteProperties->contains($favouriteProperty)) {
            $this->favouriteProperties->add($favouriteProperty);
            $favouriteProperty->setProperty($this);
        }

        return $this;
    }

    public function removeFavouriteProperty(FavouriteProperty $favouriteProperty): self
    {
        if ($this->favouriteProperties->removeElement($favouriteProperty)) {
            // set the owning side to null (unless already changed)
            if ($favouriteProperty->getProperty() === $this) {
                $favouriteProperty->setProperty(null);
            }
        }

        return $this;
    }
}
