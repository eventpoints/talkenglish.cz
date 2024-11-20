<?php

declare(strict_types=1);

namespace App\Repository;

use App\DataTransferObject\QuizFilterDto;
use App\Entity\Quiz;
use App\Enum\Quiz\CategoryEnum;
use App\Enum\Quiz\LevelEnum;
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

        if ($isQuery) {
            return $qb->getQuery();
        }

        return $qb->getQuery()->getResult();
    }
}
