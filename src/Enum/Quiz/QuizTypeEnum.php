<?php

namespace App\Enum\Quiz;

enum QuizTypeEnum: string
{
    case ILETS = 'ilets';
    case SAT = 'sat';
    case GENERAL = 'general';
    case SPEAKING = 'speaking';
    case LISTENING = 'listening';

}
