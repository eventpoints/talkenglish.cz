<?php

declare(strict_types=1);

namespace App\Repository;

use App\DataTransferObject\QuizFilterDto;
use App\Entity\Quiz;
use App\Enum\Quiz\CategoryEnum;
use App\Enum\Quiz\LevelEnum;
use Carbon\CarbonImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Quiz>
 */
class QuizRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Quiz::class);
    }

        public function save(Quiz $entity, bool $flush = false): void
    {
        $this->getEntityManager()
            ->persist($entity);

        if ($flush) {
            $this->getEntityManager()
                ->flush();
        }
    }

    public function remove(Quiz $entity, bool $flush = false): void
    {
        $this->getEntityManager()
            ->remove($entity);

        if ($flush) {
            $this->getEntityManager()
                ->flush();
        }
    }

    /**
     * @param QuizFilterDto $quizFilterDto
     * @param bool $isQuery
     * @return Query|array<int, Quiz>
     */
    public function findByQuizFilterDto(QuizFilterDto $quizFilterDto, bool $isQuery = false): Query|array
    {
        $qb = $this->createQueryBuilder('quiz');

        if ($quizFilterDto->getCategoryEnum() instanceof CategoryEnum) {
            $qb->andWhere(
                $qb->expr()->eq('quiz.categoryEnum', ':category')
            )->setParameter('category', $quizFilterDto->getCategoryEnum());
        }

        if ($quizFilterDto->getLevelEnum() instanceof LevelEnum) {
            $qb->andWhere(
                $qb->expr()->eq('quiz.levelEnum', ':level')
            )->setParameter('level', $quizFilterDto->getLevelEnum());
        }


        $qb->andWhere(
            $qb->expr()->lte('quiz.publishedAt', ':now')
        )->setParameter('now', CarbonImmutable::now());

        if ($isQuery) {
            return $qb->getQuery();
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @param Quiz|null $quiz
     * @return array<int, Quiz>
     */
    public function findRelatedByQuiz(?Quiz $quiz): array
    {
        $qb = $this->createQueryBuilder('quiz');

        $qb->andWhere(
            $qb->expr()->eq('quiz.categoryEnum', ':category')
        )->setParameter('category', $quiz->getCategoryEnum());

        $levels = LevelEnum::getSimilarLevels($quiz->getLevelEnum());
        $qb->andWhere($qb->expr()->in('quiz.levelEnum', ':levels'))
            ->setParameter('levels', $levels);

        $qb->setMaxResults(3);
        return $qb->getQuery()->getResult();
    }
}
