<?php

declare(strict_types=1);

use Symfony\Bundle\FrameworkBundle\Controller\RedirectController;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $routingConfigurator): void {

    $routingConfigurator->add('index_redirect', '/')
        ->controller(RedirectController::class)
        ->defaults([
            'route' => 'quizzes',
            'permanent' => true,
            'keepQueryParams' => true,
            'keepRequestMethod' => true,
        ]);

    $routingConfigurator->import([
        'path' => '../src/Controller/Controller',
        'namespace' => 'App\Controller\Controller',
    ], 'attribute');

    $routingConfigurator->import([
        'path' => '../src/Controller/Admin',
        'namespace' => 'App\Controller\Admin',
    ], 'attribute');
};
