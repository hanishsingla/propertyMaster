<?php

namespace App\Repository\Security;

use App\Entity\Security\User;
use App\Entity\Security\UserDetail;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserDetail>
 *
 * @method UserDetail|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserDetail|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserDetail[]    findAll()
 * @method UserDetail[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserDetailRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserDetail::class);
    }

    public function save(UserDetail $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(UserDetail $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return UserDetails[] Returns an array of UserDetails objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('u.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?UserDetails
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
    public function getUser(mixed $ownerId): ?UserDetail
    {
        return $this->findOneBy(['user' => $ownerId]);
    }

    public function getAgents(): array
    {
        $query = $this->createQueryBuilder('ud')
            ->select('ud.id, ud.city', 'ud.address', 'ud.address2', 'ud.country', 'ud.gender', 'ud.image', 'ud.name', 'ud.phone', 'ud.state', 'ud.zip', 'ud.mobile', 'u.email')
            ->innerJoin('ud.user', 'u')
            ->where('u.isAgent = :agent')
            ->setParameter('agent', true)
            ->getQuery();

        return $query->getResult();
    }
}
