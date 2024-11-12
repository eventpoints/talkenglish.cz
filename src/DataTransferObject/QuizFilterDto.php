<?php

namespace App\DataTransferObject;

use App\Enum\Quiz\CategoryEnum;
use App\Enum\Quiz\LevelEnum;

final class QuizFilterDto
{
    private null|string $keyword = null;
    private null|CategoryEnum $categoryEnum = null;
    private null|LevelEnum $levelEnum = null;

    /**
     * @param string|null $keyword
     * @param CategoryEnum|null $categoryEnum
     * @param LevelEnum|null $levelEnum
     */
    public function __construct(
        null|string       $keyword = null,
        null|CategoryEnum $categoryEnum = null,
        null|LevelEnum    $levelEnum = null
    )
    {
        $this->keyword = $keyword;
        $this->categoryEnum = $categoryEnum;
        $this->levelEnum = $levelEnum;
    }

    public function getKeyword(): ?string
    {
        return $this->keyword;
    }

    public function setKeyword(?string $keyword): void
    {
        $this->keyword = $keyword;
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