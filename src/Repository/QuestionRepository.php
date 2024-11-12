<?php

namespace App\Repository;

use App\Entity\Answer;
use App\Entity\Question;
use App\Entity\QuizParticipation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Question>
 */
class QuestionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Question::class);
    }

    public function save(Question $entity, bool $flush = false): void
    {
        $this->getEntityManager()
            ->persist($entity);

        if ($flush) {
            $this->getEntityManager()
                ->flush();
        }
    }

    public function remove(Question $entity, bool $flush = false): void
    {
        $this->getEntityManager()
            ->remove($entity);

        if ($flush) {
            $this->getEntityManager()
                ->flush();
        }
    }

    public function getOneQuestionByQuizParticipation(QuizParticipation $quizParticipation): null|Question
    {
        $qb = $this->createQueryBuilder('question');
        $qb->andWhere(
            $qb->expr()->eq('question.categoryEnum', ':categoryEnum')
        )->setParameter('categoryEnum', $quizParticipation->getCategoryEnum());

        $qb->andWhere(
            $qb->expr()->eq('question.levelEnum', ':levelEnum')
        )->setParameter('levelEnum', $quizParticipation->getLevelEnum());

        $questions = $quizParticipation->getQuestions()->map(fn($question) => $question->getId())->toArray();
        if (count($questions) > 0) {
            $qb->andWhere(
                $qb->expr()->notIn('question.id', ':questions')
            )->setParameter('questions', $questions);
        }

        $qb->setMaxResults(1);
        return $qb->getQuery()->getOneOrNullResult();
    }
}
