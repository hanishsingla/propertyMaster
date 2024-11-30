<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\MappedSuperclass]
#[ORM\HasLifecycleCallbacks]
abstract class AbstractEntity
{
    #[ORM\Column(type: 'boolean', options: ['default' => 0])]
    protected bool $isDeleted = false;

    #[ORM\Column(type: 'datetime')]
    protected \DateTime $isCreatedAt;

    #[ORM\Column(type: 'datetime', nullable: true)]
    protected \DateTime $isUpdatedAt;

    #[ORM\Column(type: 'datetime', nullable: true)]
    protected \DateTime $isDeletedAt;

    public function isDeleted(): bool
    {
        return $this->isDeleted;
    }

    public function setIsDeleted(bool $isDeleted): void
    {
        $this->isDeleted = $isDeleted;
    }

    public function getIsCreatedAt(): \DateTime
    {
        return $this->isCreatedAt;
    }

    public function setIsCreatedAt(\DateTime $isCreatedAt): void
    {
        $this->isCreatedAt = $isCreatedAt;
    }

    public function getIsUpdatedAt(): \DateTime
    {
        return $this->isUpdatedAt;
    }

    public function setIsUpdatedAt(\DateTime $isUpdatedAt): void
    {
        $this->isUpdatedAt = $isUpdatedAt;
    }

    public function getIsDeletedAt(): \DateTime
    {
        return $this->isDeletedAt;
    }

    public function setIsDeletedAt(\DateTime $isDeletedAt): void
    {
        $this->isDeletedAt = $isDeletedAt;
    }
}
