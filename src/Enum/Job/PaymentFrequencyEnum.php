<?php

declare(strict_types=1);

namespace App\Enum\Job;

enum PaymentFrequencyEnum: string
{
    case ANNUAL_RATE = 'payment.annually';
    case MONTH_RATE = 'payment.monthly';
    case WEEK_RATE = 'payment.weekly';
    case HOUR_RATE = 'payment.hourly';

}
