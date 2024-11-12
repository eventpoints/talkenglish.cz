<?php

declare(strict_types=1);

namespace App\Twig\Filter;

use App\DataTransferObject\QuestionScoreDto;
use App\Entity\Question;
use App\Entity\QuizParticipation;
use App\Service\QuizResultCalculatorService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class CalculateQuestionScoreFilter extends AbstractExtension
{
    public function __construct(
        private readonly QuizResultCalculatorService $quizResultCalculatorService
    )
    {
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('calculate_question_score', fn (QuizParticipation $quizParticipation, Question $question): float => $this->calculate($quizParticipation, $question)),
        ];
    }

    public function calculate(QuizParticipation $quizParticipation, Question $question): float
    {
        return $this->quizResultCalculatorService->calculateFractalScoreForQuestion(quizParticipation: $quizParticipation, question: $question);
    }
}
