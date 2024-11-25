<?php

declare(strict_types=1);

namespace App\Event\Subscriber;

use App\Entity\QuizParticipation;
use App\Entity\User;
use App\Enum\Quiz\CategoryEnum;
use App\Enum\Quiz\LevelEnum;
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

        if (!$entity instanceof QuizParticipation) {
            dump('Listener not triggered: Not QuizParticipation'); // Or log
            return;
        }

        if ($entity->getQuiz()->getCategoryEnum() !== CategoryEnum::LEVEL_ASSESSMENT) {
            dump('Listener not triggered: Not a Level Assessment Quiz');
            return;
        }

        if (!$entity->getCompletedAt() instanceof CarbonImmutable) {
            dump('Listener not triggered: quiz not complete yet');
            return;
        }

        // Get the currently logged-in user
        $user = $this->security->getUser();
        if (!$user instanceof User) {
            return;
        }

        // Calculate the user's level and update their profile
        $levelEnum = $this->calculateUserLevel($entity);
        $user->setLevelEnum($levelEnum);
        $user->setLevelAssessmentQuizTakenAt(new CarbonImmutable());
        $this->userRepository->save(entity: $user, flush: true);
    }

    private function calculateUserLevel(QuizParticipation $quizParticipation): LevelEnum
    {
        $score = $this->quizResultCalculatorService->calculateQuizPercentage($quizParticipation);

        return match (true) {
            default => LevelEnum::A1,
            $score > 30 && $score <= 50 => LevelEnum::A2,
            $score > 50 && $score <= 70 => LevelEnum::B1,
            $score > 70 && $score <= 85 => LevelEnum::B2,
            $score > 85 && $score <= 95 => LevelEnum::C1,
            $score > 95 => LevelEnum::C2,
        };
    }
}
