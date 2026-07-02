<?php

namespace App\Repository\Property;

use App\Entity\Property\FavouriteProperty;
use App\Entity\Property\Property;
use App\Entity\Security\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FavouriteProperty>
 *
 * @method FavouriteProperty|null find($id, $lockMode = null, $lockVersion = null)
 * @method FavouriteProperty|null findOneBy(array $criteria, array $orderBy = null)
 * @method FavouriteProperty[]    findAll()
 * @method FavouriteProperty[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FavouritePropertyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FavouriteProperty::class);
    }

    public function findOneByUserAndProperty(User $user, Property $property): ?FavouriteProperty
    {
        return $this->findOneBy(['user' => $user, 'property' => $property]);
    }

    /**
     * Non-deleted properties this user has favourited, newest first.
     *
     * @return Property[]
     */
    public function findFavouritedProperties(User $user): array
    {
        return $this->getEntityManager()->createQueryBuilder()
            ->select('p')
            ->from(Property::class, 'p')
            ->innerJoin('p.favouriteProperties', 'f')
            ->andWhere('f.user = :user')->setParameter('user', $user)
            ->andWhere('p.deletedAt IS NULL')
            ->orderBy('f.createdAt', 'DESC')
            ->getQuery()->getResult();
    }

    /**
     * Set of property ids (as array keys => true) this user has favourited,
     * optionally restricted to a candidate id list. Used to fill isFavourited
     * without an N+1.
     *
     * @param string[] $propertyIds
     *
     * @return array<string,bool>
     */
    public function favouritedIdSet(User $user, array $propertyIds = []): array
    {
        $qb = $this->createQueryBuilder('f')
            ->select('IDENTITY(f.property) AS pid')
            ->andWhere('f.user = :user')->setParameter('user', $user);

        if ([] !== $propertyIds) {
            $qb->andWhere('f.property IN (:ids)')->setParameter('ids', $propertyIds);
        }

        $set = [];
        foreach ($qb->getQuery()->getScalarResult() as $row) {
            $set[$row['pid']] = true;
        }

        return $set;
    }
}
