<?php

declare(strict_types=1);

namespace App\Service;

use App\DataTransferObject\QuizScoreDto;
use App\Entity\Answer;
use App\Entity\AnswerOption;
use App\Entity\Question;
use App\Entity\QuizParticipation;

final readonly class RadnomQuestionsResultCalculatorService
{
    public function calculateQuizPercentage(QuizParticipation $quizParticipation) : float
    {
        $questions = $quizParticipation->getQuiz()->getQuestions();
        $totalQuestions = count($questions);
        $correctAnswers = 0;

        foreach ($questions as $question) {
            $correctAnswers += $this->calculateQuestionAnswer($quizParticipation, $question);
        }

        return round($totalQuestions > 0 ? ($correctAnswers / $totalQuestions) * 100 : 0, 2) ;
    }

    public function getQuizScore(QuizParticipation $quizParticipation): QuizScoreDto
    {
        $correctCount = 0.0;
        $totalPossible = 0.0;

        foreach ($quizParticipation->getQuiz()->getQuestions() as $question) {
            $correctSelections = $this->calculateQuestionAnswer($quizParticipation, $question);

            $totalCorrectAnswers = count($question->getAnswerOptions()->filter(fn($answerOption): ?bool => $answerOption->getIsCorrect()));

            $correctCount += $correctSelections * $totalCorrectAnswers;
            $totalPossible += $totalCorrectAnswers;
        }

        return new QuizScoreDto(correct: $correctCount, possible: $totalPossible);
    }
    public function calculateQuestionAnswer(QuizParticipation $quizParticipation, Question $question) : float
    {
        $userAnswer = $quizParticipation->getAnswers()->filter(function (Answer $answer) use ($question): bool {
            return $answer->getQuestion()->getId() === $question->getId();
        })->first();

        if (!$userAnswer) {
            return 0.0;
        }

        $correctAnswerOptions = $question->getAnswerOptions()->filter(function (AnswerOption $answerOption): ?bool {
            return $answerOption->getIsCorrect();
        });

        $userSelectedOptions = $userAnswer->getAnswers();

        $correctSelections = $correctAnswerOptions->filter(fn($correctOption) => $userSelectedOptions->contains($correctOption))->count();

        $totalCorrectAnswers = count($correctAnswerOptions);
        return round($totalCorrectAnswers > 0 ? $correctSelections / $totalCorrectAnswers : 0.0, 2) ;
    }

}
