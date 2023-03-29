<?php

namespace App\Entity;

use App\Repository\PropertyRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Table(name: 'property')]
#[ORM\Entity(repositoryClass: PropertyRepository::class)]
class Property extends AbstractEntity
{
    #[ORM\Column(type: 'guid')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator('doctrine.uuid_generator')]
    #[Groups(['read'])]
    private ?string $id = null;

    #[ORM\Column]
    private string $ownerId;

    #[ORM\Column]
    private string $propertyArea;

    #[ORM\Column]
    private string $roomBed;

    #[ORM\Column]
    private string $propertyCategory;

    #[ORM\Column]
    private string $propertyCity;

    #[ORM\Column]
    private string $propertyCountry = 'india';

    #[ORM\Column]
    private string $propertyDescription;

    #[ORM\Column(nullable: true)]
    private ?array $propertyImage = null;

    #[ORM\Column]
    private string $propertyPrice;

    #[ORM\Column]
    private string $propertyRooms;

    #[ORM\Column]
    private string $propertyState;

    #[ORM\Column]
    private string $propertyTitle;

    #[ORM\Column]
    private string $propertyType;

    #[ORM\Column]
    private string $shortDescription;

    #[ORM\Column]
    private string $squareType;

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
    public function getRoomBed(): string
    {
        return $this->roomBed;
    }

    /**
     * @param string $roomBed
     */
    public function setRoomBed(string $roomBed): void
    {
        $this->roomBed = $roomBed;
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
     * @return string
     */
    public function getPropertyRooms(): string
    {
        return $this->propertyRooms;
    }

    /**
     * @param string $propertyRooms
     */
    public function setPropertyRooms(string $propertyRooms): void
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
     * @return string
     */
    public function getShortDescription(): string
    {
        return $this->shortDescription;
    }

    /**
     * @param string $shortDescription
     */
    public function setShortDescription(string $shortDescription): void
    {
        $this->shortDescription = $shortDescription;
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


}
