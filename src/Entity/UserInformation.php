<?php

namespace App\Entity;

use App\Repository\UserInformationRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Table(name: 'security_user_information')]
#[ORM\Entity(repositoryClass: UserInformationRepository::class)]
class UserInformation extends AbstractAccount
{

    #[ORM\OneToOne(mappedBy: 'userInformation', cascade: ['persist', 'remove'])]
    private ?User $user = null;

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        // set the owning side of the relation if necessary
        if ($user->getUserInformation() !== $this) {
            $user->setUserInformation($this);
        }

        $this->user = $user;

        return $this;
    }
}
