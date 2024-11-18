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
        $totalQuestions = count($quizParticipation->getQuiz()->getQuestions());
        $totalScore = $this->calculateTotalScore($quizParticipation);

        if ($totalQuestions === 0) {
            return 0;
        }

        return round(($totalScore / $totalQuestions) * 100, 2);
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
        $numCorrectSelected = 0;
        $numIncorrectSelected = 0;

        foreach ($selectedAnswerOptions as $selectedOption) {
            if ($correctAnswerOptions->exists(fn($key, $correctOption): bool => $correctOption->getId() === $selectedOption->getId())) {
                $numCorrectSelected++;
            } else {
                $numIncorrectSelected++;
            }
        }

        // Calculate the score: correct / (correct + incorrect)
        $totalSelected = $numCorrectSelected + $numIncorrectSelected;

        return $totalSelected > 0 ? round($numCorrectSelected / $totalSelected, 2) : 0.0;
    }


}
