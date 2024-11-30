<?php

namespace App\Repository\Property;

use App\Entity\Property\FavouriteProperty;
use App\Entity\Property\Property;
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

    public function save(FavouriteProperty $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(FavouriteProperty $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    //    /**
    //     * @return FavouriteProperty[] Returns an array of FavouriteProperty objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('f')
    //            ->andWhere('f.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('f.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?FavouriteProperty
    //    {
    //        return $this->createQueryBuilder('f')
    //            ->andWhere('f.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
    public function getFavoritePropertyById($propertyId, mixed $ownerId): array
    {
        return $this->findBy([
            'property' => $propertyId,
            'ownerId' => $ownerId,
        ]);
    }

    public function getFavoritePropertyByFav($ownerId): array
    {
        $query = $this->createQueryBuilder('f')
            ->select('p.id', 'p.propertyTitle', 'p.propertyCategory', 'p.propertyImage', 'p.propertyStatus', 'p.propertyCity', 'p.propertyState', 'p.propertyArea', 'p.propertyRooms', 'p.propertyPrice')
            ->innerJoin(Property::class, 'p', 'WITH', 'p.id = f.property')
            ->where('f.ownerId = :ownerId')
            ->andWhere('f.favourite != :favourite')
            ->setParameter('ownerId', $ownerId)
            ->setParameter('favourite', 'false')
        ;

        return $query->getQuery()->getResult();
    }
}
