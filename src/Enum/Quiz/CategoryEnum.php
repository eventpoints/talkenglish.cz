<?php

declare(strict_types=1);

namespace App\Enum\Quiz;

enum CategoryEnum: string
{
    case LEVEL_ASSESSMENT = 'category.level_assessment';
    case ILETS = 'category.ilets';
    case SAT = 'category.sat';
    case GENERAL = 'category.general';
    case BUSINESS = 'category.business';

    case CONVERSATIONAL = 'category.conversation';
}
