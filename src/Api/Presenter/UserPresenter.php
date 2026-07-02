<?php

namespace App\Api\Presenter;

use App\Entity\Security\User;
use Symfony\Component\HttpFoundation\UrlHelper;

class UserPresenter
{
    public function __construct(private readonly UrlHelper $urlHelper)
    {
    }

    /**
     * Full self view (GET /api/me, /api/account).
     *
     * @return array<string, mixed>
     */
    public function self(User $user): array
    {
        return [
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'name' => $user->getName(),
            'roles' => $user->getRoles(),
            'isAgent' => $user->isAgent(),
            'isVerified' => $user->isVerified(),
            'gender' => $user->getGender()?->value,
            'avatarUrl' => $this->avatarUrl($user),
            'phone' => $user->getPhone(),
            'mobile' => $user->getMobile(),
            'country' => $user->getCountry(),
            'address' => $user->getAddress(),
            'address2' => $user->getAddress2(),
            'city' => $user->getCity(),
            'state' => $user->getState(),
            'zip' => $user->getZip(),
            'createdAt' => $user->getCreatedAt()->format(\DateTimeInterface::ATOM),
        ];
    }

    /**
     * Public agent card (GET /api/agents).
     *
     * @return array<string, mixed>
     */
    public function agent(User $user): array
    {
        return [
            'id' => $user->getId(),
            'name' => $user->getName(),
            'isAgent' => $user->isAgent(),
            'avatarUrl' => $this->avatarUrl($user),
            'phone' => $user->getPhone(),
            'mobile' => $user->getMobile(),
            'city' => $user->getCity(),
        ];
    }

    private function avatarUrl(User $user): ?string
    {
        $avatar = $user->getAvatar();
        if (null === $avatar) {
            return null;
        }

        return $this->urlHelper->getAbsoluteUrl('/uploads/avatars/'.$avatar);
    }
}
