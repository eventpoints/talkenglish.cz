<?php

namespace App\Message\Handler;

use App\Entity\EmailTransmission;
use App\Entity\Quiz;
use App\Entity\QuizParticipation;
use App\Entity\User;
use App\Message\Message\WeeklyQuizEmailNotification;
use App\Repository\EmailTransmissionRepository;
use App\Repository\QuizParticipationRepository;
use App\Repository\UserRepository;
use App\Repository\WeeklyQuizRepository;
use App\Service\EmailService;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class WeeklyQuizEmailNotificationHandler
{

    public function __construct(
        private readonly UserRepository              $userRepository,
        private readonly WeeklyQuizRepository        $weeklyQuizRepository,
        private readonly EmailService                $emailService,
        private readonly EmailTransmissionRepository $emailTransmissionRepository,
        private readonly LoggerInterface             $logger
    )
    {
    }

    /**
     * @throws TransportExceptionInterface
     */
    public
    function __invoke(WeeklyQuizEmailNotification $weeklyQuizEmailNotification)
    {
        // 1. Get a random user who has not received an email this week.
        $user = $this->userRepository->getUserWhoHasNotReceivedQuizEmailThisWeek();

        // 2. Get the current weekly quiz
        $weeklyQuiz = $this->weeklyQuizRepository->getCurrentWeeklyQuiz();

        $this->logger->info("sending weekly quiz: {$weeklyQuiz->getQuiz()->getTitle()} to {$user->getFullName()}");
        $this->emailService->sendQuizEmail(emailAddress: $user->getEmail(), quiz: $weeklyQuiz->getQuiz(), context: [
            'user' => $user,
            'quiz' => $weeklyQuiz->getQuiz()
        ]);

        $emailTransmission = new EmailTransmission(owner: $user, quiz: $weeklyQuiz->getQuiz());
        $this->emailTransmissionRepository->save(entity: $emailTransmission, flush: true);
    }
}