<?php

namespace App\Service;

use App\Entity\Property\FavouriteProperty;
use App\Entity\Property\Property;
use App\Entity\Security\User;
use App\Repository\Property\FavouritePropertyRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;

class FavouriteService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly FavouritePropertyRepository $repository,
    ) {
    }

    /**
     * Idempotent: favouriting an already-favourited property is a no-op.
     */
    public function add(User $user, Property $property): void
    {
        if (null !== $this->repository->findOneByUserAndProperty($user, $property)) {
            return;
        }

        $favourite = (new FavouriteProperty())
            ->setUser($user)
            ->setProperty($property);

        try {
            $this->em->persist($favourite);
            $this->em->flush();
        } catch (UniqueConstraintViolationException) {
            // Concurrent double-add; treat as success.
        }
    }

    public function remove(User $user, Property $property): void
    {
        $favourite = $this->repository->findOneByUserAndProperty($user, $property);
        if (null !== $favourite) {
            $this->em->remove($favourite);
            $this->em->flush();
        }
    }
}
