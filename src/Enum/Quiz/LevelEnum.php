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
}
