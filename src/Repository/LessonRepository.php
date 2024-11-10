<?php

namespace App\Repository;

use App\Entity\Lesson;
use Carbon\CarbonImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Order;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Lesson>
 */
class LessonRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Lesson::class);
    }

    public function save(Lesson $entity, bool $flush = false): void
    {
        $this->getEntityManager()
            ->persist($entity);

        if ($flush) {
            $this->getEntityManager()
                ->flush();
        }
    }

    public function remove(Lesson $entity, bool $flush = false): void
    {
        $this->getEntityManager()
            ->remove($entity);

        if ($flush) {
            $this->getEntityManager()
                ->flush();
        }
    }


    /**
     * @return array<int, Lesson>
     */
    public function findAvailableLessons(): array
    {
        $qb = $this->createQueryBuilder('lesson');

        $qb->andWhere(
            $qb->expr()->gt('lesson.endAt', ':now')
        )->setParameter('now', CarbonImmutable::now());

        $qb->orderBy(sort: 'lesson.startAt', order: Order::Ascending->value);
        return $qb->getQuery()->getResult();
    }
}
