<?php

namespace App\Repository\Security;

use App\Entity\Security\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);

        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    public function getUser(string $ownerId): ?User
    {
        return $this->findOneBy(['id' => $ownerId]);
    }

    public function fetchAllData(string $ownerId): ?array
    {
        return $this->createQueryBuilder('u')
            ->where('u.id = :ownerId')
            ->setParameter('ownerId', $ownerId)
            ->getQuery()
            ->getResult();
    }

    public function getAgents(): array
    {
        return $this->createQueryBuilder('ud')
            ->select('ud.id, ud.city', 'ud.address', 'ud.address2', 'ud.country', 'ud.gender', 'ud.image', 'ud.name', 'ud.phone', 'ud.state', 'ud.zip', 'ud.mobile', 'u.email')
            ->innerJoin('ud.user', 'u')
            ->where('u.isAgent = :agent')
            ->setParameter('agent', true)
            ->getQuery()
            ->getResult();
    }
}
