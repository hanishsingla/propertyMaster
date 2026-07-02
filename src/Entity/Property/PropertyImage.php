<?php

namespace App\Entity\Property;

use App\Entity\AbstractEntity;
use App\Repository\Property\PropertyImageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Table(name: 'property_images')]
#[ORM\Index(name: 'index_id', columns: ['id'])]
#[ORM\Entity(repositoryClass: PropertyImageRepository::class)]
class PropertyImage extends AbstractEntity
{
    #[ORM\Column(type: Types::GUID)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator('doctrine.uuid_generator')]
    #[Groups(['property:read'])]
    private ?string $id = null;

    #[ORM\ManyToOne(targetEntity: Property::class, inversedBy: 'images')]
    #[ORM\JoinColumn(name: 'property_id', referencedColumnName: 'id', nullable: false)]
    private ?Property $property = null;

    /** Stored filename, relative to /uploads/properties/{propertyId}/. */
    #[ORM\Column(length: 255)]
    #[Groups(['property:read'])]
    private string $path;

    #[ORM\Column(type: Types::INTEGER, options: ['default' => 0])]
    #[Groups(['property:read'])]
    private int $sortOrder = 0;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => 0])]
    #[Groups(['property:read'])]
    private bool $isCover = false;

    public function getId(): ?string
    {
        return $this->id;
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

    public function getPath(): string
    {
        return $this->path;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function getSortOrder(): int
    {
        return $this->sortOrder;
    }

    public function setSortOrder(int $sortOrder): self
    {
        $this->sortOrder = $sortOrder;

        return $this;
    }

    public function isCover(): bool
    {
        return $this->isCover;
    }

    public function setIsCover(bool $isCover): self
    {
        $this->isCover = $isCover;

        return $this;
    }
}
