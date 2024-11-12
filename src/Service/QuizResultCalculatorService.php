<?php

namespace App\Service;

use App\DataTransferObject\QuestionScoreDto;
use App\DataTransferObject\QuizScoreDto;
use App\Entity\Question;
use App\Entity\QuizParticipation;

class QuizResultCalculatorService
{
    public function calculateQuizPercentage(QuizParticipation $quizParticipation): float
    {
        $totalQuestions = count($quizParticipation->getQuestions());
        $totalScore = $this->calculateTotalScore($quizParticipation);

        if ($totalQuestions === 0) {
            return 0;
        }

        return round(($totalScore / $totalQuestions) * 100, 2);
    }

    private function calculateTotalScore(QuizParticipation $quizParticipation): float
    {
        $totalScore = 0;
        foreach ($quizParticipation->getQuestions() as $question) {
            $totalScore += $this->calculateFractalScoreForQuestion($quizParticipation, $question);
        }
        return round($totalScore, 2);
    }

    public function calculateFractalScoreForQuestion(QuizParticipation $quizParticipation, $question): float
    {
        $userAnswers = $quizParticipation->getAnswers()->filter(
            fn($answer) => $answer->getQuestion()->getId() === $question->getId()
        );

        $correctAnswerOptions = $question->getAnswerOptions()->filter(fn($option) => $option->isCorrect());

        $numCorrectAnswers = $correctAnswerOptions->count();
        $numCorrectSelected = $userAnswers->filter(
            fn($answer) => $correctAnswerOptions->contains($answer)
        )->count();

        return round($numCorrectAnswers > 0 ? $numCorrectSelected / $numCorrectAnswers : 0, 2);
    }
}
