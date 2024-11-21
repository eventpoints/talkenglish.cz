<?php

namespace App\Enum\Quiz;

enum SupportingContentTypeEnum: int
{
    case TEXT = 1;
    case AUDIO = 2;
    case IMAGE = 3;
    case VIDEO = 4;

}
