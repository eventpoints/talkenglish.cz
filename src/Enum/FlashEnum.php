<?php

declare(strict_types=1);

namespace App\Enum;

enum FlashEnum : string
{
    case SUCCESS = 'success';
    case DANGER = 'danger';
    case INFO = 'info';
    case WARNING = 'warning';

}
