<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\MappedSuperclass]
#[ORM\HasLifecycleCallbacks]
abstract class AbstractEntity
{
    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::BOOLEAN, options: ['default' => 0])]
    protected bool $isDeleted = false;

    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::DATETIME_MUTABLE)]
    protected \DateTime $isCreatedAt;

    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::DATETIME_MUTABLE, nullable: true)]
    protected \DateTime $isUpdatedAt;

    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::DATETIME_MUTABLE, nullable: true)]
    protected \DateTime $isDeletedAt;

    public function isDeleted(): bool
    {
        return $this->isDeleted;
    }

    public function setIsDeleted(bool $isDeleted): self
    {
        $this->isDeleted = $isDeleted;

        return $this;
    }

    public function getIsCreatedAt(): \DateTime
    {
        return $this->isCreatedAt;
    }

    public function setIsCreatedAt(\DateTime $isCreatedAt): self
    {
        $this->isCreatedAt = $isCreatedAt;

        return $this;
    }

    public function getIsUpdatedAt(): \DateTime
    {
        return $this->isUpdatedAt;
    }

    public function setIsUpdatedAt(\DateTime $isUpdatedAt): self
    {
        $this->isUpdatedAt = $isUpdatedAt;

        return $this;
    }

    public function getIsDeletedAt(): \DateTime
    {
        return $this->isDeletedAt;
    }

    public function setIsDeletedAt(\DateTime $isDeletedAt): self
    {
        $this->isDeletedAt = $isDeletedAt;

        return $this;
    }
}
