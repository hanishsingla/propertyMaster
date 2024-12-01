<?php

namespace App\Entity\Property;

use App\Entity\AbstractEntity;
use App\Repository\Property\PropertyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Table(name: 'properties')]
#[ORM\Index(name: 'index_id', columns: ['id'])]
#[ORM\Entity(repositoryClass: PropertyRepository::class)]
class Property extends AbstractEntity
{
    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::GUID)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator('doctrine.uuid_generator')]
    #[Groups(['read'])]
    private ?string $id = null;

    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::STRING)]
    private string $ownerId;

    #[ORM\Column]
    private string $propertyArea;

    #[ORM\Column]
    private string $propertyBathRooms;

    #[ORM\Column]
    private string $propertyCategory;

    #[ORM\Column]
    private string $propertyCity;

    #[ORM\Column]
    private string $propertyState = 'Punjab';

    #[ORM\Column]
    private string $propertyCountry = 'India';

    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::TEXT)]
    private string $propertyDescription;

    #[ORM\Column]
    private string $propertyDirection;

    #[ORM\Column(nullable: true)]
    private ?array $propertyImage = null;

    #[ORM\Column]
    private string $propertyPrice;

    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::STRING, nullable: true)]
    private ?string $propertyRooms = null;

    #[ORM\Column]
    private string $propertyStatus;

    #[ORM\Column]
    private string $propertyTitle;

    #[ORM\Column]
    private string $propertyType;

    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::STRING, nullable: true)]
    private ?string $propertyBedRooms = null;

    #[ORM\Column]
    private string $squareType = 'feet';

    /**
     * @var Collection<int, FavouriteProperty>
     */
    #[ORM\OneToMany(targetEntity: FavouriteProperty::class, mappedBy: 'property', cascade: ['persist', 'remove'])]
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

    public function setOwnerId(string $ownerId): self
    {
        $this->ownerId = $ownerId;

        return $this;
    }

    public function getPropertyArea(): string
    {
        return $this->propertyArea;
    }

    public function setPropertyArea(string $propertyArea): self
    {
        $this->propertyArea = $propertyArea;

        return $this;
    }

    public function getPropertyBathRooms(): string
    {
        return $this->propertyBathRooms;
    }

    public function setPropertyBathRooms(string $propertyBathRooms): self
    {
        $this->propertyBathRooms = $propertyBathRooms;

        return $this;
    }

    public function getPropertyCategory(): string
    {
        return $this->propertyCategory;
    }

    public function setPropertyCategory(string $propertyCategory): self
    {
        $this->propertyCategory = $propertyCategory;

        return $this;
    }

    public function getPropertyCity(): string
    {
        return $this->propertyCity;
    }

    public function setPropertyCity(string $propertyCity): self
    {
        $this->propertyCity = $propertyCity;

        return $this;
    }

    public function getPropertyCountry(): string
    {
        return $this->propertyCountry;
    }

    public function setPropertyCountry(string $propertyCountry): self
    {
        $this->propertyCountry = $propertyCountry;

        return $this;
    }

    public function getPropertyDescription(): string
    {
        return $this->propertyDescription;
    }

    public function setPropertyDescription(string $propertyDescription): self
    {
        $this->propertyDescription = $propertyDescription;

        return $this;
    }

    public function getPropertyDirection(): string
    {
        return $this->propertyDirection;
    }

    public function setPropertyDirection(string $propertyDirection): self
    {
        $this->propertyDirection = $propertyDirection;

        return $this;
    }

    public function getPropertyImage(): ?array
    {
        return $this->propertyImage;
    }

    public function setPropertyImage(?array $propertyImage): self
    {
        $this->propertyImage = $propertyImage;

        return $this;
    }

    public function getPropertyPrice(): string
    {
        return $this->propertyPrice;
    }

    public function setPropertyPrice(string $propertyPrice): self
    {
        $this->propertyPrice = $propertyPrice;

        return $this;
    }

    public function getPropertyRooms(): ?string
    {
        return $this->propertyRooms;
    }

    public function setPropertyRooms(?string $propertyRooms): self
    {
        $this->propertyRooms = $propertyRooms;

        return $this;
    }

    public function getPropertyState(): string
    {
        return $this->propertyState;
    }

    public function setPropertyState(string $propertyState): self
    {
        $this->propertyState = $propertyState;

        return $this;
    }

    public function getPropertyStatus(): string
    {
        return $this->propertyStatus;
    }

    public function setPropertyStatus(string $propertyStatus): self
    {
        $this->propertyStatus = $propertyStatus;

        return $this;
    }

    public function getPropertyTitle(): string
    {
        return $this->propertyTitle;
    }

    public function setPropertyTitle(string $propertyTitle): self
    {
        $this->propertyTitle = $propertyTitle;

        return $this;
    }

    public function getPropertyType(): string
    {
        return $this->propertyType;
    }

    public function setPropertyType(string $propertyType): self
    {
        $this->propertyType = $propertyType;

        return $this;
    }

    public function getPropertyBedRooms(): ?string
    {
        return $this->propertyBedRooms;
    }

    public function setPropertyBedRooms(?string $propertyBedRooms): self
    {
        $this->propertyBedRooms = $propertyBedRooms;

        return $this;
    }

    public function getSquareType(): string
    {
        return $this->squareType;
    }

    public function setSquareType(string $squareType): self
    {
        $this->squareType = $squareType;

        return $this;
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
        // set the owning side to null (unless already changed)
        if ($this->favouriteProperties->removeElement($favouriteProperty) && $favouriteProperty->getProperty() === $this) {
            $favouriteProperty->setProperty(null);
        }

        return $this;
    }
}
