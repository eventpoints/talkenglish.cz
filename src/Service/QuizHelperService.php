<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Answer;
use App\Entity\Question;
use App\Entity\QuizParticipation;
use Carbon\CarbonImmutable;

class QuizHelperService
{
    public function isQuizCompleted(QuizParticipation $quizParticipation): bool
    {
        return $quizParticipation->getCompletedAt() instanceof CarbonImmutable
            || new CarbonImmutable() >= $quizParticipation->getCalculatedQuizEndAt();
    }

    public function getNextUnansweredQuestion(QuizParticipation $quizParticipation): ?Question
    {
        $questions = $quizParticipation->getQuiz()->getQuestions();
        $answeredQuestions = $quizParticipation->getAnswers()->map(fn(Answer $answer): ?Question => $answer->getQuestion())->toArray();

        foreach ($questions as $question) {
            if (!in_array($question, $answeredQuestions, true)) {
                return $question;
            }
        }

        return null;
    }
}
