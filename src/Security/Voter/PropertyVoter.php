<?php

namespace App\Security\Voter;

use App\Entity\Property\Property;
use App\Entity\Security\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * @extends Voter<self::EDIT|self::DELETE, Property>
 */
class PropertyVoter extends Voter
{
    public const EDIT = 'PROPERTY_EDIT';
    public const DELETE = 'PROPERTY_DELETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::EDIT, self::DELETE], true) && $subject instanceof Property;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, ?Vote $vote = null): bool
    {
        $user = $token->getUser();
        if (!$user instanceof User) {
            return false;
        }

        // Admins can edit/delete any property.
        if (in_array('ROLE_ADMIN', $user->getRoles(), true)) {
            return true;
        }

        /* @var Property $subject */
        return $subject->getOwner()?->getId() === $user->getId();
    }
}
