<?php

namespace App\Entity\Address;

use App\Entity\AbstractAccount;
use App\Entity\Security\User;
use App\Repository\Information\UserAddressRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'security_user_address')]
#[ORM\Entity(repositoryClass: UserAddressRepository::class)]
class UserAddress extends AbstractAccount
{
    #[ORM\OneToOne(inversedBy: 'userAddress', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
