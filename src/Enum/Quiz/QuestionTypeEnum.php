<?php

namespace App\Enum\Quiz;

enum QuestionTypeEnum : string
{
    case MULTIPLE_CHOICE = 'multi_choice';
    case FILL_IN_THE_BLACK = 'fill_in_the_blank';
    case TRUE_OR_FALSE = 'true_or_false';
    case MATCHING = 'matching';
    case COMPREHENSION = 'comprehension';
}
