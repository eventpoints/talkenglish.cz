<?php

namespace App\Repository;

use App\Entity\Lesson;
use App\Entity\Quiz;
use App\Entity\QuizParticipation;
use App\Entity\User;
use Carbon\CarbonImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use function Doctrine\ORM\QueryBuilder;

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

}
