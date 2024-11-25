<?php

declare(strict_types=1);

namespace App\Event\Subscriber;

use App\Entity\Quiz;
use App\Entity\User;
use App\Enum\Quiz\CategoryEnum;
use App\Enum\RoleEnum;
use App\Exception\ShouldNotHappenException;
use App\Repository\QuizRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[AsEventListener(event: RequestEvent::class, method: 'setLevelAssessmentQuiz', priority: 7)]
readonly class LevelAssessmentQuizSubscriber
{

    public function __construct(
        private Security              $security,
        private UrlGeneratorInterface $urlGenerator,
        private QuizRepository        $quizRepository
    )
    {
    }

    /**
     * @throws ShouldNotHappenException
     */
    public function setLevelAssessmentQuiz(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $user = $this->security->getUser();

        if (!$user instanceof User) {
            return;
        }

        if (!$this->security->isGranted(attributes: RoleEnum::STUDENT->value, subject: $user)) {
            return;
        }

        $route = $event->getRequest()->get('_route');
        if ($route === 'quiz_start' || $route === 'quiz_in_progress') {
            return;
        }

        if (!$user->getLevelAssessmentQuizTakenAt() instanceof \Carbon\CarbonImmutable && !$user->getLevelEnum() instanceof \App\Enum\Quiz\LevelEnum) {
            $quiz = $this->quizRepository->findOneBy(['categoryEnum' => CategoryEnum::LEVEL_ASSESSMENT]);

            if(!$quiz instanceof Quiz){
                throw new ShouldNotHappenException(message: 'No level assessment quiz found.');
            }

            $redirectUrl = $this->urlGenerator->generate('quiz_start', ['id' => $quiz->getId(), 'post-signup-check' => 1]);
            $event->setResponse(new RedirectResponse(url: $redirectUrl));
        }
    }
}
