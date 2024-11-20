<?php

declare(strict_types=1);

use App\Kernel;

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

return function (array $context): \App\Kernel {
    date_default_timezone_set("utc");
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
