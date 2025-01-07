<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\User;
use Carbon\CarbonImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
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
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    public function save(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()
            ->persist($entity);

        if ($flush) {
            $this->getEntityManager()
                ->flush();
        }
    }

    public function remove(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()
            ->remove($entity);

        if ($flush) {
            $this->getEntityManager()
                ->flush();
        }
    }

    public function getUserWhoHasNotReceivedQuizEmailThisWeek(): null|User
    {
        $qb = $this->createQueryBuilder('user');
        $qb->leftJoin('user.emailTransmissions', 'email_transmission');

        $qb->andWhere(
            $qb->expr()->orX(
                $qb->expr()->isNull('email_transmission.id'),
                $qb->expr()->lt('email_transmission.createdAt', ':startOfWeek')
            )
        )->setParameter('startOfWeek', CarbonImmutable::now()->startOfWeek()->toDateTimeImmutable());

        $qb->andWhere(
            $qb->expr()->eq('user.isSubscribedToWeeklyQuizEmail', ':true')
        )->setParameter('true', true, Types::BOOLEAN);

        $qb->setMaxResults(1);
        return $qb->getQuery()->getOneOrNullResult();
    }
}
