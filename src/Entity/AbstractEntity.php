<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\MappedSuperclass]
#[ORM\HasLifecycleCallbacks]
abstract class AbstractEntity
{
    #[ORM\Column(type: 'guid')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator('doctrine.uuid_generator')]
    #[Groups(['read'])]
    private ?string $id = null;

    #[ORM\Column(type: 'boolean' ,options: ['default' => 0] )]
    protected bool $isDeleted = false;

    #[ORM\Column(type: 'datetime')]
    protected DateTime $isCreatedAt;

    #[ORM\Column(type: 'datetime' , nullable: true)]
    protected DateTime $isUpdatedAt ;

    #[ORM\Column(type: 'datetime' , nullable: true)]
    protected DateTime $isDeletedAt ;


    /**
     * @return string|null
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @param string|null $id
     */
    public function setId(?string $id): void
    {
        $this->id = $id;
    }

    /**
     * @return bool
     */
    public function isDeleted(): bool
    {
        return $this->isDeleted;
    }

    /**
     * @param bool $isDeleted
     */
    public function setIsDeleted(bool $isDeleted): void
    {
        $this->isDeleted = $isDeleted;
    }

    /**
     * @return DateTime
     */
    public function getIsCreatedAt(): DateTime
    {
        return $this->isCreatedAt;
    }

    /**
     * @param DateTime $isCreatedAt
     */
    public function setIsCreatedAt(DateTime $isCreatedAt): void
    {
        $this->isCreatedAt = $isCreatedAt;
    }

    /**
     * @return DateTime
     */
    public function getIsUpdatedAt(): DateTime
    {
        return $this->isUpdatedAt;
    }

    /**
     * @param DateTime $isUpdatedAt
     */
    public function setIsUpdatedAt(DateTime $isUpdatedAt): void
    {
        $this->isUpdatedAt = $isUpdatedAt;
    }

    /**
     * @return DateTime
     */
    public function getIsDeletedAt(): DateTime
    {
        return $this->isDeletedAt;
    }

    /**
     * @param DateTime $isDeletedAt
     */
    public function setIsDeletedAt(DateTime $isDeletedAt): void
    {
        $this->isDeletedAt = $isDeletedAt;
    }
}
