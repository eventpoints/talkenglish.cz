<?php

namespace App\DataTransferObject;

final readonly class QuizParticipationStatistic
{
    public function __construct(
        private int $participantsCount,
        private float $betterThanPercentage,
        private float $percentile,
    )
    {
    }

    public function getParticipantsCount(): int
    {
        return $this->participantsCount;
    }

    public function getBetterThanPercentage(): float
    {
        return $this->betterThanPercentage;
    }

    public function getPercentile(): float
    {
        return $this->percentile;
    }

}