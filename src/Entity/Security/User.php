<?php

namespace App\Entity\Security;

use App\Entity\AbstractEntity;
use App\Enum\Gender;
use App\Repository\Security\UserRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'security_users')]
#[ORM\Index(name: 'index_id', columns: ['id'])]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User extends AbstractEntity implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Column(type: Types::GUID)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator('doctrine.uuid_generator')]
    #[Groups(['user:read', 'agent:read', 'property:read'])]
    private ?string $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Groups(['user:self'])]
    private ?string $email = null;

    /** @var list<string> */
    #[ORM\Column]
    #[Groups(['user:self'])]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private string $password;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => 0])]
    #[Groups(['user:read', 'agent:read'])]
    private bool $isAgent = false;

    #[ORM\Column(type: Types::BOOLEAN)]
    #[Groups(['user:self'])]
    private bool $isVerified = false;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    #[Groups(['user:read', 'agent:read', 'property:read'])]
    private ?string $name = null;

    #[ORM\Column(type: Types::STRING, length: 20, nullable: true, enumType: Gender::class)]
    #[Groups(['user:read'])]
    private ?Gender $gender = null;

    /** Avatar filename, served from /uploads/avatars/. */
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    #[Groups(['user:read', 'agent:read', 'property:read'])]
    private ?string $avatar = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    #[Groups(['user:self', 'agent:read'])]
    private ?string $phone = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    #[Groups(['user:self', 'agent:read'])]
    private ?string $mobile = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    #[Groups(['user:self'])]
    private ?string $country = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    #[Groups(['user:self'])]
    private ?string $address = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    #[Groups(['user:self'])]
    private ?string $address2 = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    #[Groups(['user:self'])]
    private ?string $city = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    #[Groups(['user:self'])]
    private ?string $zip = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    #[Groups(['user:self'])]
    private ?string $state = null;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function isAgent(): bool
    {
        return $this->isAgent;
    }

    public function setIsAgent(bool $isAgent): self
    {
        $this->isAgent = $isAgent;

        return $this;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getGender(): ?Gender
    {
        return $this->gender;
    }

    public function setGender(?Gender $gender): self
    {
        $this->gender = $gender;

        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getMobile(): ?string
    {
        return $this->mobile;
    }

    public function setMobile(?string $mobile): self
    {
        $this->mobile = $mobile;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getAddress2(): ?string
    {
        return $this->address2;
    }

    public function setAddress2(?string $address2): self
    {
        $this->address2 = $address2;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getZip(): ?string
    {
        return $this->zip;
    }

    public function setZip(?string $zip): self
    {
        $this->zip = $zip;

        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(?string $state): self
    {
        $this->state = $state;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     *
     * @return string[]
     */
    #[Groups(['user:self'])]
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        // isAgent flag drives ROLE_AGENT (see role_hierarchy in security.yaml)
        if ($this->isAgent) {
            $roles[] = 'ROLE_AGENT';
        }

        return array_values(array_unique($roles));
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
    }
}
