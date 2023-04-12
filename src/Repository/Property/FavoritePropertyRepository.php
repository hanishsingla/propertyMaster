<?php

namespace App\Repository\Property;

use App\Entity\Property\FavoriteProperty;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FavoriteProperty>
 *
 * @method FavoriteProperty|null find($id, $lockMode = null, $lockVersion = null)
 * @method FavoriteProperty|null findOneBy(array $criteria, array $orderBy = null)
 * @method FavoriteProperty[]    findAll()
 * @method FavoriteProperty[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FavoritePropertyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FavoriteProperty::class);
    }

    public function save(FavoriteProperty $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(FavoriteProperty $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return FavoriteProperty[] Returns an array of FavoriteProperty objects
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

//    public function findOneBySomeField($value): ?FavoriteProperty
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
    public function getFavoritePropertyById($propertyId, mixed $ownerId): ?FavoriteProperty
    {
        return $this->findOneBy([
            'property' => $propertyId,
            'ownerId'=>$ownerId,
        ]);
    }
}
