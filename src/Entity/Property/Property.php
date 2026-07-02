<?php

namespace App\Entity\Property;

use App\Entity\AbstractEntity;
use App\Entity\Security\User;
use App\Enum\AreaUnit;
use App\Enum\Direction;
use App\Enum\ListingType;
use App\Enum\PropertyCategory;
use App\Enum\PropertyStatus;
use App\Enum\PropertyType;
use App\Repository\Property\PropertyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Table(name: 'properties')]
#[ORM\Index(name: 'index_id', columns: ['id'])]
#[ORM\Index(name: 'index_slug', columns: ['slug'])]
#[ORM\Index(name: 'index_status', columns: ['status'])]
#[ORM\Entity(repositoryClass: PropertyRepository::class)]
class Property extends AbstractEntity
{
    #[ORM\Column(type: Types::GUID)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator('doctrine.uuid_generator')]
    #[Groups(['property:list', 'property:read'])]
    private ?string $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'owner_id', referencedColumnName: 'id', nullable: false)]
    #[Groups(['property:read'])]
    private ?User $owner = null;

    #[ORM\Column(length: 180)]
    #[Groups(['property:list', 'property:read'])]
    private string $title;

    #[ORM\Column(length: 200, unique: true)]
    #[Groups(['property:list', 'property:read'])]
    private string $slug;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['property:read'])]
    private string $description;

    #[ORM\Column(length: 20, enumType: ListingType::class)]
    #[Groups(['property:list', 'property:read'])]
    private ListingType $listingType;

    #[ORM\Column(length: 40, enumType: PropertyCategory::class)]
    #[Groups(['property:list', 'property:read'])]
    private PropertyCategory $category;

    #[ORM\Column(length: 20, enumType: PropertyType::class)]
    #[Groups(['property:read'])]
    private PropertyType $type;

    #[ORM\Column(length: 20, enumType: PropertyStatus::class, options: ['default' => 'draft'])]
    #[Groups(['property:read'])]
    private PropertyStatus $status = PropertyStatus::Draft;

    /** Price in minor units (paise). BIGINT hydrates to string in DBAL; int accessors below. */
    #[ORM\Column(type: Types::BIGINT)]
    private string $priceMinor = '0';

    #[ORM\Column(length: 3, options: ['default' => 'INR'])]
    #[Groups(['property:list', 'property:read'])]
    private string $currency = 'INR';

    #[ORM\Column(type: Types::INTEGER)]
    #[Groups(['property:list', 'property:read'])]
    private int $area = 0;

    #[ORM\Column(length: 10, enumType: AreaUnit::class, options: ['default' => 'sq_ft'])]
    #[Groups(['property:list', 'property:read'])]
    private AreaUnit $areaUnit = AreaUnit::SquareFeet;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    #[Groups(['property:list', 'property:read'])]
    private ?int $bedRooms = null;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    #[Groups(['property:list', 'property:read'])]
    private ?int $bathRooms = null;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    #[Groups(['property:read'])]
    private ?int $rooms = null;

    #[ORM\Column(length: 10, nullable: true, enumType: Direction::class)]
    #[Groups(['property:read'])]
    private ?Direction $direction = null;

    #[ORM\Column]
    #[Groups(['property:list', 'property:read'])]
    private string $city;

    #[ORM\Column(options: ['default' => 'Punjab'])]
    #[Groups(['property:read'])]
    private string $state = 'Punjab';

    #[ORM\Column(options: ['default' => 'India'])]
    #[Groups(['property:read'])]
    private string $country = 'India';

    #[ORM\Column(type: Types::FLOAT, nullable: true)]
    #[Groups(['property:read'])]
    private ?float $latitude = null;

    #[ORM\Column(type: Types::FLOAT, nullable: true)]
    #[Groups(['property:read'])]
    private ?float $longitude = null;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => 0])]
    #[Groups(['property:list', 'property:read'])]
    private bool $isFeatured = false;

    /**
     * @var Collection<int, PropertyImage>
     */
    #[ORM\OneToMany(targetEntity: PropertyImage::class, mappedBy: 'property', cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[ORM\OrderBy(['sortOrder' => 'ASC'])]
    #[Groups(['property:read'])]
    private Collection $images;

    /**
     * @var Collection<int, FavouriteProperty>
     */
    #[ORM\OneToMany(targetEntity: FavouriteProperty::class, mappedBy: 'property', cascade: ['persist', 'remove'])]
    private Collection $favouriteProperties;

    public function __construct()
    {
        $this->images = new ArrayCollection();
        $this->favouriteProperties = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getListingType(): ListingType
    {
        return $this->listingType;
    }

    public function setListingType(ListingType $listingType): self
    {
        $this->listingType = $listingType;

        return $this;
    }

    public function getCategory(): PropertyCategory
    {
        return $this->category;
    }

    public function setCategory(PropertyCategory $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getType(): PropertyType
    {
        return $this->type;
    }

    public function setType(PropertyType $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getStatus(): PropertyStatus
    {
        return $this->status;
    }

    public function setStatus(PropertyStatus $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getPriceMinor(): int
    {
        return (int) $this->priceMinor;
    }

    public function setPriceMinor(int $priceMinor): self
    {
        $this->priceMinor = (string) $priceMinor;

        return $this;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): self
    {
        $this->currency = $currency;

        return $this;
    }

    public function getArea(): int
    {
        return $this->area;
    }

    public function setArea(int $area): self
    {
        $this->area = $area;

        return $this;
    }

    public function getAreaUnit(): AreaUnit
    {
        return $this->areaUnit;
    }

    public function setAreaUnit(AreaUnit $areaUnit): self
    {
        $this->areaUnit = $areaUnit;

        return $this;
    }

    public function getBedRooms(): ?int
    {
        return $this->bedRooms;
    }

    public function setBedRooms(?int $bedRooms): self
    {
        $this->bedRooms = $bedRooms;

        return $this;
    }

    public function getBathRooms(): ?int
    {
        return $this->bathRooms;
    }

    public function setBathRooms(?int $bathRooms): self
    {
        $this->bathRooms = $bathRooms;

        return $this;
    }

    public function getRooms(): ?int
    {
        return $this->rooms;
    }

    public function setRooms(?int $rooms): self
    {
        $this->rooms = $rooms;

        return $this;
    }

    public function getDirection(): ?Direction
    {
        return $this->direction;
    }

    public function setDirection(?Direction $direction): self
    {
        $this->direction = $direction;

        return $this;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function setState(string $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    public function setCountry(string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(?float $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(?float $longitude): self
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function isFeatured(): bool
    {
        return $this->isFeatured;
    }

    public function setIsFeatured(bool $isFeatured): self
    {
        $this->isFeatured = $isFeatured;

        return $this;
    }

    /**
     * @return Collection<int, PropertyImage>
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(PropertyImage $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images->add($image);
            $image->setProperty($this);
        }

        return $this;
    }

    public function removeImage(PropertyImage $image): self
    {
        if ($this->images->removeElement($image) && $image->getProperty() === $this) {
            $image->setProperty(null);
        }

        return $this;
    }

    public function getCoverImage(): ?PropertyImage
    {
        foreach ($this->images as $image) {
            if ($image->isCover()) {
                return $image;
            }
        }

        return $this->images->first() ?: null;
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
        if ($this->favouriteProperties->removeElement($favouriteProperty) && $favouriteProperty->getProperty() === $this) {
            $favouriteProperty->setProperty(null);
        }

        return $this;
    }
}
