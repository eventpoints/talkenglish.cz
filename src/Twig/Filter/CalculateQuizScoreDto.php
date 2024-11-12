<?php

declare(strict_types=1);

namespace App\Twig\Filter;

use App\DataTransferObject\QuizScoreDto;
use App\Entity\Question;
use App\Entity\QuizParticipation;
use App\Service\QuizResultCalculatorService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class CalculateQuizScoreDto extends AbstractExtension
{
    public function __construct(
        private readonly QuizResultCalculatorService $quizResultCalculatorService
    )
    {
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('calculate_quiz_score', fn (QuizParticipation $quizParticipation): QuizScoreDto => $this->calculate($quizParticipation)),
        ];
    }

    public function calculate(QuizParticipation $quizParticipation): QuizScoreDto
    {
        return $this->quizResultCalculatorService->getQuizScore(quizParticipation: $quizParticipation);
    }
}
