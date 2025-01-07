<?php

declare(strict_types=1);

namespace App\Repository;

use App\DataTransferObject\QuizParticipationStatistic;
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
     * @return QuizParticipation|null
     */
    public function findLastTaken(Quiz $quiz, User $user): null|QuizParticipation
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
        $qb->setMaxResults(1);

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function getUserPerformanceStats(QuizParticipation $quizParticipation): QuizParticipationStatistic
    {

        $qb = $this->createQueryBuilder('quiz_participation');
        $qb->leftJoin('quiz_participation.quiz', 'quiz');

        $quizIdCount = $qb->expr()->count('quiz.id');
        $qb->select("$quizIdCount as totalParticipants");

        $qb->addSelect('SUM(CASE WHEN quiz_participation.score < :score THEN 1 ELSE 0 END) as betterThanCount')
            ->setParameter('score', $quizParticipation->getScore());

        $qb->andWhere('quiz_participation.quiz = :quizId')
            ->setParameter('quizId', $quizParticipation->getQuiz()->getId(), 'uuid');

        $qb->andWhere(
            $qb->expr()->neq('quiz_participation.owner', ':owner')
        )->setParameter('owner', $quizParticipation->getOwner()->getId(), 'uuid');

        $result = $qb->getQuery()->getSingleResult();

        $totalParticipants = (int)$result['totalParticipants'];
        $betterThanCount = (int)$result['betterThanCount'];

        $betterThanPercentage = $totalParticipants > 0
            ? round(($betterThanCount / $totalParticipants) * 100, 2)
            : 0;

        $percentile = 100 - $betterThanPercentage;

        return new QuizParticipationStatistic(
            participantsCount: $totalParticipants,
            betterThanPercentage: $betterThanPercentage,
            percentile: $percentile
        );
    }

    /**
     * @param User $user
     * @return array<int, QuizParticipation>
     */
    public function findByUser(User $user) : array
    {
        $qb = $this->createQueryBuilder('quiz_participation');

        $qb->andWhere(
            $qb->expr()->eq('quiz_participation.owner' ,':owner')
        )->setParameter('owner', $user->getId(), 'uuid');

        $qb->andWhere(
            $qb->expr()->isNotNull('quiz_participation.completedAt')
        );

        return $qb->getQuery()->getResult();
    }

}
