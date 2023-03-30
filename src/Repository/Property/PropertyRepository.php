<?php

namespace App\Repository\Property;

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

    public function save(Property $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Property $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Property[] Returns an array of Property objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Property
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    public function getPropertyByListType($listType)
    {
        $qb = $this->createQueryBuilder('p');

        if ($listType !== 'all') {
            $qb->andWhere('p.propertyCategory = :listType')
                ->setParameter('listType', $listType);
        }

        $qb->setMaxResults(4);

        return $qb->getQuery()->getResult();
    }

    public function getSearchProperty(?string $city, ?string $propertyType, ?string $status)
    {
        $qb = $this->createQueryBuilder('p')
            ->Where('p.propertyCity = :city')
            ->andWhere('p.propertyType = :propertyType')
            ->andWhere('p.propertyStatus = :status')
            ->setParameter('city', $city)
            ->setParameter('propertyType', $propertyType)
            ->setParameter('status', $status);

        return $qb->getQuery()->getResult();
    }

    public function getProperty(?string $propertyId, $ownerId): ?Property
    {
        return $this->findOneBy([
            'id' => $propertyId,
            'ownerId'=>$ownerId,
        ]);
    }

}
