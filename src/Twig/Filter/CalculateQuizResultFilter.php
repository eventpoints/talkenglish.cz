<?php

declare(strict_types=1);

namespace App\Twig\Filter;

use App\Entity\QuizParticipation;
use App\Service\QuizResultCalculatorService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class CalculateQuizResultFilter extends AbstractExtension
{
    public function __construct(
        private readonly QuizResultCalculatorService $quizResultCalculatorService
    )
    {
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('calculate_quiz_percentage', fn (QuizParticipation $quizParticipation): float => $this->calculate($quizParticipation)),
        ];
    }

    public function calculate(QuizParticipation $quizParticipation): float
    {
        return $this->quizResultCalculatorService->calculateQuizPercentage(quizParticipation: $quizParticipation);
    }
}
