<?php

namespace App\Repository;

use App\Entity\Quiz;
use App\Entity\WeeklyQuiz;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Order;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<WeeklyQuiz>
 */
class WeeklyQuizRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WeeklyQuiz::class);
    }

    public function getCurrentWeeklyQuiz(): null|WeeklyQuiz
    {
        $qb = $this->createQueryBuilder('weekly_quiz');
        $qb->orderBy('weekly_quiz.createdAt', Order::Descending->value);
        $qb->setMaxResults(1);

        return $qb->getQuery()->getOneOrNullResult();
    }
}
