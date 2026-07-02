<?php

namespace App\Repository\Property;

use App\Entity\Property\Property;
use App\Enum\PropertyStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
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

    public function findActiveById(string $id): ?Property
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.id = :id')->setParameter('id', $id)
            ->andWhere('p.deletedAt IS NULL')
            ->getQuery()->getOneOrNullResult();
    }

    public function findActiveByIdOrSlug(string $idOrSlug): ?Property
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.id = :v OR p.slug = :v')->setParameter('v', $idOrSlug)
            ->andWhere('p.deletedAt IS NULL')
            ->getQuery()->getOneOrNullResult();
    }

    /**
     * @param array<string,mixed> $filters
     *
     * @return array{items: Property[], total: int}
     */
    public function search(array $filters, int $page, int $perPage): array
    {
        $qb = $this->createQueryBuilder('p')
            ->andWhere('p.deletedAt IS NULL');

        $this->applyFilters($qb, $filters);
        $this->applySort($qb, $filters['sort'] ?? 'newest');

        $qb->setFirstResult(($page - 1) * $perPage)->setMaxResults($perPage);

        $paginator = new Paginator($qb->getQuery(), true);

        return [
            'items' => iterator_to_array($paginator),
            'total' => count($paginator),
        ];
    }

    /**
     * @param array<string,mixed> $filters
     */
    private function applyFilters(QueryBuilder $qb, array $filters): void
    {
        // Status resolution:
        //  - explicit PropertyStatus  -> filter by it
        //  - allStatuses = true        -> no status filter (owner/admin views)
        //  - otherwise                 -> public default: published only
        if (isset($filters['status']) && $filters['status'] instanceof PropertyStatus) {
            $qb->andWhere('p.status = :status')->setParameter('status', $filters['status']);
        } elseif (empty($filters['allStatuses'])) {
            $qb->andWhere('p.status = :published')->setParameter('published', PropertyStatus::Published);
        }

        if (!empty($filters['owner'])) {
            $qb->andWhere('p.owner = :owner')->setParameter('owner', $filters['owner']);
        }
        if (!empty($filters['listingType'])) {
            $qb->andWhere('p.listingType = :listingType')->setParameter('listingType', $filters['listingType']);
        }
        if (!empty($filters['type'])) {
            $qb->andWhere('p.type = :type')->setParameter('type', $filters['type']);
        }
        if (!empty($filters['category'])) {
            $qb->andWhere('p.category = :category')->setParameter('category', $filters['category']);
        }
        if (!empty($filters['city'])) {
            $qb->andWhere('LOWER(p.city) = LOWER(:city)')->setParameter('city', $filters['city']);
        }
        if (isset($filters['minPrice'])) {
            $qb->andWhere('p.priceMinor >= :minPrice')->setParameter('minPrice', $filters['minPrice']);
        }
        if (isset($filters['maxPrice'])) {
            $qb->andWhere('p.priceMinor <= :maxPrice')->setParameter('maxPrice', $filters['maxPrice']);
        }
        if (isset($filters['bedRooms'])) {
            $qb->andWhere('p.bedRooms >= :bedRooms')->setParameter('bedRooms', $filters['bedRooms']);
        }
        if (isset($filters['isFeatured'])) {
            $qb->andWhere('p.isFeatured = :isFeatured')->setParameter('isFeatured', $filters['isFeatured']);
        }
        if (!empty($filters['q'])) {
            $qb->andWhere('LOWER(p.title) LIKE :q OR LOWER(p.description) LIKE :q OR LOWER(p.city) LIKE :q')
                ->setParameter('q', '%'.strtolower($filters['q']).'%');
        }
    }

    private function applySort(QueryBuilder $qb, string $sort): void
    {
        match ($sort) {
            'price_asc' => $qb->orderBy('p.priceMinor', 'ASC'),
            'price_desc' => $qb->orderBy('p.priceMinor', 'DESC'),
            default => $qb->orderBy('p.createdAt', 'DESC'),
        };
    }
}
