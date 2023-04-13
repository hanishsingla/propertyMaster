<?php

namespace App\Repository\Property;

use App\Entity\Property\FavouriteProperty;
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
        return $this->findBy([
            'ownerId' => $ownerId,
            'favourite'=> 'true'
        ]);
    }
}
