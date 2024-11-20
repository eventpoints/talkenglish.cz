<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Quiz;
use App\Entity\QuizParticipation;
use App\Entity\User;
use Carbon\CarbonImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Order;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<QuizParticipation>
 */
class QuizParticipationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, QuizParticipation::class);
    }

    public function save(QuizParticipation $entity, bool $flush = false): void
    {
        $this->getEntityManager()
            ->persist($entity);

        if ($flush) {
            $this->getEntityManager()
                ->flush();
        }
    }

    public function remove(QuizParticipation $entity, bool $flush = false): void
    {
        $this->getEntityManager()
            ->remove($entity);

        if ($flush) {
            $this->getEntityManager()
                ->flush();
        }
    }

    public function findInProgressByUser(Quiz $quiz, User $user): null|QuizParticipation
    {
        $qb = $this->createQueryBuilder('quiz_participation');
        $now = CarbonImmutable::now();
        $endAt = $now->subMinutes($quiz->getTimeLimitInMinutes());

        $qb->leftJoin('quiz_participation.quiz', 'quiz');

        $qb->andWhere(
            $qb->expr()->eq('quiz_participation.quiz', ':quiz')
        );
        $qb->setParameter('quiz', $quiz);

        $qb->andWhere(
            $qb->expr()->eq('quiz_participation.owner', ':owner')
        );
        $qb->setParameter('owner', $user);

        $qb->andWhere(
            $qb->expr()->isNull('quiz_participation.completedAt')
        );

        $qb->andWhere(
            $qb->expr()->gt('quiz_participation.startAt', ':endAt')
        );
        $qb->setParameter('endAt', $endAt);
        $qb->setMaxResults(1);

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * @param Quiz $quiz
     * @param User $user
     * @return array<int, QuizParticipation>
     */
    public function findByUser(Quiz $quiz, User $user):  array
    {
        $qb = $this->createQueryBuilder('quiz_participation');

        $qb->andWhere(
            $qb->expr()->eq('quiz_participation.quiz', ':quiz')
        );
        $qb->setParameter('quiz', $quiz);

        $qb->andWhere(
            $qb->expr()->eq('quiz_participation.owner', ':owner')
        );
        $qb->setParameter('owner', $user);

        $qb->orderBy('quiz_participation.startAt', Order::Descending->value);
        $qb->setMaxResults(5);

        return $qb->getQuery()->getResult();
    }

}
