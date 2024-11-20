<?php

declare(strict_types=1);

namespace App\DataTransferObject;

use App\Entity\Question;

final readonly class QuestionScoreDto
{
    private Question $question;
    private float $score;

    /**
     * @param Question $question
     * @param float $score
     */
    public function __construct(Question $question, float $score)
    {
        $this->question = $question;
        $this->score = $score;
    }

    public function getQuestion(): Question
    {
        return $this->question;
    }

    public function getScore(): float
    {
        return $this->score;
    }

}
