<?php

declare(strict_types=1);

namespace App\Repository;

use App\DataTransferObject\LessonFilterDto;
use App\Entity\Lesson;
use App\Enum\Quiz\CategoryEnum;
use App\Enum\Quiz\LevelEnum;
use Carbon\CarbonImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Order;
use Doctrine\ORM\Query;
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

    /**
     * @param LessonFilterDto $lessonFilterDto
     * @param bool $isQuery
     * @return Query|array<int, Lesson>
     */
    public function findByLessonFilterDto(LessonFilterDto $lessonFilterDto, bool $isQuery = false): Query|array
    {
        $qb = $this->createQueryBuilder('lesson');

        if ($lessonFilterDto->getCategoryEnum() instanceof CategoryEnum) {
            $qb->andWhere(
                $qb->expr()->eq('lesson.categoryEnum', ':category')
            )->setParameter('category', $lessonFilterDto->getCategoryEnum());
        }

        if ($lessonFilterDto->getLevelEnum() instanceof LevelEnum) {
            $qb->andWhere(
                $qb->expr()->eq('lesson.levelEnum', ':level')
            )->setParameter('level', $lessonFilterDto->getLevelEnum());
        }

        if ($isQuery) {
            return $qb->getQuery();
        }

        return $qb->getQuery()->getResult();
    }
}
