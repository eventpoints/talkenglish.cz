<?php

declare(strict_types=1);

namespace App\Event\Subscriber;

use App\Entity\QuizParticipation;
use App\Entity\User;
use App\Enum\Quiz\CategoryEnum;
use App\Repository\UserRepository;
use App\Service\QuizResultCalculatorService;
use Carbon\CarbonImmutable;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Doctrine\ORM\Events;
use Symfony\Bundle\SecurityBundle\Security;

#[AsDoctrineListener(event: Events::postUpdate)]
class UserLevelExtractionSubscriber
{
    public function __construct(
        private Security                    $security,
        private QuizResultCalculatorService $quizResultCalculatorService,
        private UserRepository              $userRepository
    )
    {
    }

    public function postUpdate(PostUpdateEventArgs $postUpdateEventArgs): void
    {
        $entity = $postUpdateEventArgs->getObject();

        // Not QuizParticipation
        if (!$entity instanceof QuizParticipation) {
            return;
        }

        // Not a Level Assessment Quiz
        if ($entity->getQuiz()->getCategoryEnum() !== CategoryEnum::LEVEL_ASSESSMENT) {
            return;
        }

        // quiz not complete yet
        if (!$entity->getCompletedAt() instanceof CarbonImmutable) {
            return;
        }

        // Get the currently logged-in user
        $user = $this->security->getUser();
        if (!$user instanceof User) {
            return;
        }

        $levelEnum = $this->quizResultCalculatorService->getLevelAssessmentScore($entity);
        $user->setLevelEnum($levelEnum);
        $user->setLevelAssessmentQuizTakenAt(new CarbonImmutable());
        $this->userRepository->save(entity: $user, flush: true);
    }
}
