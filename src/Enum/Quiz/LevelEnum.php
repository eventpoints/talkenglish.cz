<?php

declare(strict_types=1);

namespace App\Enum\Quiz;

enum LevelEnum : string
{
    case A1 = 'level.beginner';
    case A2 = 'level.pre_intermediate';
    case B1 = 'level.intermediate';
    case B2 = 'level.upper_intermediate';
    case C1 = 'level.advanced';
    case C2 = 'level.mastery';

    /**
     * @param LevelEnum $currentLevel
     * @return LevelEnum[]
     */
    public static function getSimilarLevels(LevelEnum $currentLevel): array
    {
        $levels = [
            LevelEnum::A1,
            LevelEnum::A2,
            LevelEnum::B1,
            LevelEnum::B2,
            LevelEnum::C1,
            LevelEnum::C2,
        ];

        $index = array_search($currentLevel, $levels, true);
        if ($index === false) {
            return [$currentLevel]; // Fallback: return only the current level
        }

        $similarLevels = [$levels[$index]]; // Always include current level

        if (isset($levels[$index - 1])) {
            $similarLevels[] = $levels[$index - 1]; // Previous level
        }
        if (isset($levels[$index + 1])) {
            $similarLevels[] = $levels[$index + 1]; // Next level
        }

        return $similarLevels;
    }
}
