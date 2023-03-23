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
    private string $propertyTitle;

    #[ORM\Column]
    private string $propertyPrice;

    #[ORM\Column]
    private string $propertyArea;

    #[ORM\Column]
    private string $propertyAddress;

    #[ORM\Column]
    private string $shortDescription;
    #[ORM\Column]
    private string $propertyDescription;


    #[ORM\Column]
    private string $propertyRooms;


    #[ORM\Column]
    private string $propertyStatus;

    #[ORM\Column]
    private string $propertyType;

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
    public function getPropertyAddress(): string
    {
        return $this->propertyAddress;
    }

    /**
     * @param string $propertyAddress
     */
    public function setPropertyAddress(string $propertyAddress): void
    {
        $this->propertyAddress = $propertyAddress;
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

}
