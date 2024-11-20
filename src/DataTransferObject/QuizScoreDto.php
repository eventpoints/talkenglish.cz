<?php

declare(strict_types=1);

namespace App\DataTransferObject;

final readonly class QuizScoreDto
{
    private float $correct;
    private float $possible;

    /**
     * @param float $correct
     * @param float $possible
     */
    public function __construct(float $correct, float $possible)
    {
        $this->correct = $correct;
        $this->possible = $possible;
    }

    public function getCorrect(): float
    {
        return $this->correct;
    }

    public function getPossible(): float
    {
        return $this->possible;
    }

}
