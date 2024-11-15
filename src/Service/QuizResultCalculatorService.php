<?php

namespace App\Service;

use App\Entity\Answer;
use App\Entity\AnswerOption;
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

        $numCorrectSelected = 0;
        foreach ($selectedAnswerOptions as $selectedOption) {
            foreach ($correctAnswerOptions as $correctOption) {
                if ($selectedOption->getId() === $correctOption->getId()) {
                    $numCorrectSelected++;
                }
            }
        }

        $numCorrectAnswers = $correctAnswerOptions->count();
        return $numCorrectAnswers > 0 ? round($numCorrectSelected / $numCorrectAnswers, 2) : 0.0;
    }

}
