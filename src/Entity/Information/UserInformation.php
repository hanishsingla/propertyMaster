<?php

namespace App\Entity\Information;

use App\Entity\AbstractAccount;
use App\Entity\Security\User;
use App\Repository\Information\UserInformationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'security_user_information')]
#[ORM\Entity(repositoryClass: UserInformationRepository::class)]
class UserInformation extends AbstractAccount
{
    #[ORM\OneToOne(inversedBy: 'userInformation', cascade: ['persist', 'remove'])]
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
