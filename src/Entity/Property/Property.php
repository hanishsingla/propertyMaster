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

    #[ORM\Column(type: "string")]
    private string $ownerId;

    #[ORM\Column(type: 'boolean' ,options: ['default' => 0])]
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

    #[ORM\Column(type: "string" ,length: "1000")]
    private string $propertyDescription;

    #[ORM\Column]
    private string $propertyDirection;

    #[ORM\Column (nullable: true)]
    private ?string $propertyGarage = null;

    #[ORM\Column(nullable: true)]
    private ?array $propertyImage = null;

    #[ORM\Column]
    private string $propertyPrice;

    #[ORM\Column(type: "string",nullable: true)]
    private ?string $propertyRooms = null;

    #[ORM\Column]
    private string $propertyState;

    #[ORM\Column]
    private string $propertyStatus;

    #[ORM\Column]
    private string $propertyTitle;

    #[ORM\Column]
    private string $propertyType;

    #[ORM\Column(type: "string",nullable: true)]
    private ?string  $propertyBedRooms= null;

    #[ORM\Column]
    private string $squareType;

    #[ORM\OneToMany(mappedBy: 'property', targetEntity: FavouriteProperty::class , cascade: ['persist', 'remove'])]
    private Collection $favouriteProperties;

    public function __construct()
    {
        $this->favouriteProperties = new ArrayCollection();
    }

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
    public function isPropertyIsGarage(): bool
    {
        return $this->propertyIsGarage;
    }

    /**
     * @param bool $propertyIsGarage
     */
    public function setPropertyIsGarage(bool $propertyIsGarage): void
    {
        $this->propertyIsGarage = $propertyIsGarage;
    }


    /**
     * @return string
     */
    public function getPropertyArea(): string
    {
        return $this->propertyArea;
    }

    /**
     * @param string $propertyArea
     */
    public function setPropertyArea(string $propertyArea): void
    {
        $this->propertyArea = $propertyArea;
    }

    /**
     * @return string
     */
    public function getPropertyBathRooms(): string
    {
        return $this->propertyBathRooms;
    }

    /**
     * @param string $propertyBathRooms
     */
    public function setPropertyBathRooms(string $propertyBathRooms): void
    {
        $this->propertyBathRooms = $propertyBathRooms;
    }

    /**
     * @return string
     */
    public function getPropertyCategory(): string
    {
        return $this->propertyCategory;
    }

    /**
     * @param string $propertyCategory
     */
    public function setPropertyCategory(string $propertyCategory): void
    {
        $this->propertyCategory = $propertyCategory;
    }

    /**
     * @return string
     */
    public function getPropertyCity(): string
    {
        return $this->propertyCity;
    }

    /**
     * @param string $propertyCity
     */
    public function setPropertyCity(string $propertyCity): void
    {
        $this->propertyCity = $propertyCity;
    }

    /**
     * @return string
     */
    public function getPropertyCountry(): string
    {
        return $this->propertyCountry;
    }

    /**
     * @param string $propertyCountry
     */
    public function setPropertyCountry(string $propertyCountry): void
    {
        $this->propertyCountry = $propertyCountry;
    }

    /**
     * @return string
     */
    public function getPropertyDescription(): string
    {
        return $this->propertyDescription;
    }

    /**
     * @param string $propertyDescription
     */
    public function setPropertyDescription(string $propertyDescription): void
    {
        $this->propertyDescription = $propertyDescription;
    }

    /**
     * @return string
     */
    public function getPropertyDirection(): string
    {
        return $this->propertyDirection;
    }

    /**
     * @param string $propertyDirection
     */
    public function setPropertyDirection(string $propertyDirection): void
    {
        $this->propertyDirection = $propertyDirection;
    }

    /**
     * @return string|null
     */
    public function getPropertyGarage(): ?string
    {
        return $this->propertyGarage;
    }

    /**
     * @param string|null $propertyGarage
     */
    public function setPropertyGarage(?string $propertyGarage): void
    {
        $this->propertyGarage = $propertyGarage;
    }


    /**
     * @return array|null
     */
    public function getPropertyImage(): ?array
    {
        return $this->propertyImage;
    }

    /**
     * @param array|null $propertyImage
     */
    public function setPropertyImage(?array $propertyImage): void
    {
        $this->propertyImage = $propertyImage;
    }

    /**
     * @return string
     */
    public function getPropertyPrice(): string
    {
        return $this->propertyPrice;
    }

    /**
     * @param string $propertyPrice
     */
    public function setPropertyPrice(string $propertyPrice): void
    {
        $this->propertyPrice = $propertyPrice;
    }

    /**
     * @return string|null
     */
    public function getPropertyRooms(): ?string
    {
        return $this->propertyRooms;
    }

    /**
     * @param string|null $propertyRooms
     */
    public function setPropertyRooms(?string $propertyRooms): void
    {
        $this->propertyRooms = $propertyRooms;
    }

    /**
     * @return string
     */
    public function getPropertyState(): string
    {
        return $this->propertyState;
    }

    /**
     * @param string $propertyState
     */
    public function setPropertyState(string $propertyState): void
    {
        $this->propertyState = $propertyState;
    }

    /**
     * @return string
     */
    public function getPropertyStatus(): string
    {
        return $this->propertyStatus;
    }

    /**
     * @param string $propertyStatus
     */
    public function setPropertyStatus(string $propertyStatus): void
    {
        $this->propertyStatus = $propertyStatus;
    }

    /**
     * @return string
     */
    public function getPropertyTitle(): string
    {
        return $this->propertyTitle;
    }

    /**
     * @param string $propertyTitle
     */
    public function setPropertyTitle(string $propertyTitle): void
    {
        $this->propertyTitle = $propertyTitle;
    }

    /**
     * @return string
     */
    public function getPropertyType(): string
    {
        return $this->propertyType;
    }

    /**
     * @param string $propertyType
     */
    public function setPropertyType(string $propertyType): void
    {
        $this->propertyType = $propertyType;
    }

    /**
     * @return string|null
     */
    public function getPropertyBedRooms(): ?string
    {
        return $this->propertyBedRooms;
    }

    /**
     * @param string|null $propertyBedRooms
     */
    public function setPropertyBedRooms(?string $propertyBedRooms): void
    {
        $this->propertyBedRooms = $propertyBedRooms;
    }

    /**
     * @return string
     */
    public function getSquareType(): string
    {
        return $this->squareType;
    }

    /**
     * @param string $squareType
     */
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
