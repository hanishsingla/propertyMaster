<?php

namespace App\Repository\Property;

use App\Entity\Property\FavouriteProperty;
use App\Entity\Property\Property;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Property>
 *
 * @method Property|null find($id, $lockMode = null, $lockVersion = null)
 * @method Property|null findOneBy(array $criteria, array $orderBy = null)
 * @method Property[]    findAll()
 * @method Property[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PropertyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Property::class);
    }

    public function getAllProperty(): array
    {
        return $this->findAll();
    }

    public function getProperty(?string $propertyId): ?Property
    {
        return $this->findOneBy([
            'id' => $propertyId,
        ]);
    }

    public function getOwnerProperties(string $ownerId): array
    {
        return $this->findBy([
            'ownerId' => $ownerId,
        ]);
    }

    public function getOwnerProperty(string $ownerId, string $id): ?Property
    {
        return $this->findOneBy([
            'id' => $id,
            'ownerId' => $ownerId,
        ]);
    }

    public function getPropertyByListType(string $listType, ?int $maxResult): array
    {
        $qb = $this->createQueryBuilder('p');

        if ('all' !== $listType) {
            $qb->andWhere('p.propertyStatus = :listType')
                ->setParameter('listType', $listType);
        }

        return $qb
            ->setMaxResults($maxResult)
            ->getQuery()
            ->getResult();
    }

    public function getSearchProperty(?string $city, ?string $propertyCategory, ?string $status, ?string $propertyType): array
    {
        return $this->createQueryBuilder('p')
            ->Where('p.propertyCity = :city')
            ->andWhere('p.propertyCategory = :propertyCategory')
            ->andWhere('p.propertyStatus = :status')
            ->setParameter('city', $city)
            ->setParameter('propertyCategory', $propertyCategory)
            ->setParameter('status', $status)
            ->getQuery()
            ->getResult();
    }

    public function getFavoritePropertyOneByFav(string $propertyId, string $ownerId): array
    {
        return $this->createQueryBuilder('p')
            ->leftJoin(FavouriteProperty::class, 'f', 'With', 'p.id = f.property')
            ->where('p.id = :propertyId')
            ->andWhere('p.ownerId = :ownerId')
            ->setParameter('propertyId', $propertyId)
            ->setParameter('ownerId', $ownerId)
            ->getQuery()
            ->getResult();
    }
}
