<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Answer;
use App\Entity\AnswerOption;
use App\Entity\Question;
use App\Entity\QuizParticipation;
use App\Enum\Quiz\LevelEnum;

class QuizResultCalculatorService
{
    public function calculateQuizPercentage(QuizParticipation $quizParticipation): float
    {
        $totalQuestions = count($quizParticipation->getQuiz()->getQuestions());
        $totalScore = $this->calculateTotalScore($quizParticipation);

        if ($totalQuestions === 0) {
            return 0;
        }

        return round(($totalScore / $totalQuestions) * 100);
    }

    private function calculateTotalScore(QuizParticipation $quizParticipation): float
    {
        $totalScore = 0;

        foreach ($quizParticipation->getQuiz()->getQuestions() as $question) {
            $totalScore += $this->calculateFractalScoreForQuestion($quizParticipation, $question);
        }

        return round($totalScore, 2);
    }

    public function calculateFractalScoreForQuestion(QuizParticipation $quizParticipation, Question $question): float
    {
        $userAnswers = $quizParticipation->getAnswers()->filter(
            fn(Answer $answer): bool => $answer->getQuestion() === $question
        );

        $correctAnswerOptions = $question->getAnswerOptions()->filter(
            fn(AnswerOption $answerOption): bool => $answerOption->getIsCorrect()
        );

        $selectedAnswerOptions = [];
        foreach ($userAnswers as $userAnswer) {
            foreach ($userAnswer->getAnswers() as $selectedOption) {
                $selectedAnswerOptions[] = $selectedOption;
            }
        }

        // Ensure unique selected options to avoid double-counting
        $selectedAnswerOptions = array_unique($selectedAnswerOptions);

        // Count correct and incorrect selections
        $correctSelectedCount = 0;
        $incorrectSelectedCount = 0;

        foreach ($selectedAnswerOptions as $selectedOption) {
            if ($correctAnswerOptions->exists(fn($key, $correctOption): bool => $correctOption->getId() === $selectedOption->getId())) {
                $correctSelectedCount++;
            } else {
                $incorrectSelectedCount++;
            }
        }

        // Calculate score using partial credit: correct / total correct - incorrect / total incorrect
        $totalCorrect = count($correctAnswerOptions);
        $totalIncorrect = count($question->getAnswerOptions()) - $totalCorrect;

        $score = 0.0;
        if ($totalCorrect > 0) {
            $score += $correctSelectedCount / $totalCorrect;
        }

        if ($totalIncorrect > 0) {
            $score -= $incorrectSelectedCount / $totalIncorrect;
        }

        // Ensure the score doesn't go below zero
        return max(0.0, round($score, 2));
    }

    public function getLevelAssessmentScore(QuizParticipation $quizParticipation): LevelEnum
    {
        $score = $this->calculateQuizPercentage($quizParticipation);

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
