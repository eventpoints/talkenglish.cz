<?php

declare(strict_types=1);

namespace App\Twig\Filter;

use App\Entity\QuizParticipation;
use App\Enum\Quiz\LevelEnum;
use App\Service\QuizResultCalculatorService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class LevelAssessmentScoreFilter extends AbstractExtension
{
    public function __construct(
        private readonly QuizResultCalculatorService $quizResultCalculatorService
    )
    {
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('calculate_level_assessment_score', fn (QuizParticipation $quizParticipation): LevelEnum => $this->calculate($quizParticipation)),
        ];
    }

    public function calculate(QuizParticipation $quizParticipation): LevelEnum
    {
        return $this->quizResultCalculatorService->getLevelAssessmentScore(quizParticipation: $quizParticipation);
    }
}
