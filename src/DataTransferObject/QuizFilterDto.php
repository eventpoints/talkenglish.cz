<?php

declare(strict_types=1);

namespace App\DataTransferObject;

use App\Enum\Quiz\CategoryEnum;
use App\Enum\Quiz\LevelEnum;

final class QuizFilterDto
{
    private null|CategoryEnum $categoryEnum = null;
    private null|LevelEnum $levelEnum = null;

    /**
     * @param CategoryEnum|null $categoryEnum
     * @param LevelEnum|null $levelEnum
     */
    public function __construct(
        null|CategoryEnum $categoryEnum = null,
        null|LevelEnum    $levelEnum = null
    )
    {
        $this->categoryEnum = $categoryEnum;
        $this->levelEnum = $levelEnum;
    }

    public function getCategoryEnum(): ?CategoryEnum
    {
        return $this->categoryEnum;
    }

    public function setCategoryEnum(?CategoryEnum $categoryEnum): void
    {
        $this->categoryEnum = $categoryEnum;
    }

    public function getLevelEnum(): ?LevelEnum
    {
        return $this->levelEnum;
    }

    public function setLevelEnum(?LevelEnum $levelEnum): void
    {
        $this->levelEnum = $levelEnum;
    }

}
