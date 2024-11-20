<?php

declare(strict_types=1);

namespace App\DataTransferObject;

use App\Enum\Quiz\LevelEnum;
use App\Enum\Quiz\CategoryEnum;

final class QuizConfigurationDto
{
    private null|CategoryEnum $questionCategoryEnum;
    private null|LevelEnum $questionLevelEnum;
    private bool $isTimed;

    /**
     * @param CategoryEnum|null $questionCategoryEnum
     * @param LevelEnum|null $questionLevelEnum
     * @param bool $isTimed
     */
    public function __construct(
        null|CategoryEnum $questionCategoryEnum = CategoryEnum::GENERAL,
        null|LevelEnum    $questionLevelEnum = LevelEnum::A1,
        bool              $isTimed = false,
    )
    {
        $this->questionCategoryEnum = $questionCategoryEnum;
        $this->questionLevelEnum = $questionLevelEnum;
        $this->isTimed = $isTimed;
    }


    public function isTimed(): bool
    {
        return $this->isTimed;
    }

    public function setIsTimed(bool $isTimed): void
    {
        $this->isTimed = $isTimed;
    }


    public function getQuestionCategoryEnum(): ?CategoryEnum
    {
        return $this->questionCategoryEnum;
    }

    public function setQuestionCategoryEnum(?CategoryEnum $questionCategoryEnum): void
    {
        $this->questionCategoryEnum = $questionCategoryEnum;
    }

    public function getQuestionLevelEnum(): ?LevelEnum
    {
        return $this->questionLevelEnum;
    }

    public function setQuestionLevelEnum(?LevelEnum $questionLevelEnum): void
    {
        $this->questionLevelEnum = $questionLevelEnum;
    }


}
