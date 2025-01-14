<?php

declare(strict_types=1);

namespace App\Event\Subscriber;

use App\Entity\User;
use App\Repository\QuizParticipationRepository;
use App\Service\QuizHelperService;
use App\Service\QuizResultCalculatorService;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\RequestEvent;

#[AsEventListener(event: RequestEvent::class, method: 'resolveIncompleteQuizzes', priority: 7)]
readonly class IncompleteQuizSubscriber
{

    public function __construct(
        private Security                    $security,
        private QuizParticipationRepository $quizParticipationRepository,
        private QuizHelperService $quizHelperService,
        private QuizResultCalculatorService $quizResultCalculatorService
    )
    {
    }

    public function resolveIncompleteQuizzes(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $user = $this->security->getUser();
        if (!$user instanceof User) {
            return;
        }

        $quizParticipations = $this->quizParticipationRepository->findIncompleteQuizzes(user: $user);

        if (empty($quizParticipations)) {
            return;
        }

        foreach ($quizParticipations as $quizParticipation) {
            $this->quizHelperService->completeQuiz(quizParticipation: $quizParticipation, quizResultCalculatorService: $this->quizResultCalculatorService);
            $this->quizParticipationRepository->save(entity: $quizParticipation, flush: true);
        }

    }
}
