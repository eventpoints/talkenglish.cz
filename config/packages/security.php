<?php

declare(strict_types=1);

use App\Entity\User;
use App\Enum\RoleEnum;
use App\Security\CustomAuthenticator;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('security', [
        'password_hashers' => [
            PasswordAuthenticatedUserInterface::class => 'auto',
        ],
        'providers' => [
            'app_user_provider' => [
                'entity' => [
                    'class' => User::class,
                    'property' => 'email',
                ],
            ],
        ],
        'firewalls' => [
            'dev' => [
                'pattern' => '^/(_(profiler|wdt)|css|images|js)/',
                'security' => false,
            ],
            'main' => [
                'lazy' => true,
                'provider' => 'app_user_provider',
                'custom_authenticator' => CustomAuthenticator::class,
                'entry_point' => CustomAuthenticator::class,
                'form_login' => [
                    'login_path' => 'app_login',
                    'check_path' => 'app_login',
                    'enable_csrf' => true
                ],
                'logout' => [
                    'path' => 'app_logout',
                ],
                'remember_me' => [
                    'secret' => '%kernel.secret%',
                    'lifetime' => 604800,
                    'path' => '/',
                ],
            ],
        ],
        'access_control' => [
            [
                'path' => '/admin',
                'roles' => [RoleEnum::ADMIN->value]
            ],
            [
                'path' => '/user',
                'roles' => [RoleEnum::STUDENT->value, RoleEnum::TEACHER->value]
            ],
            [
                'path' => '/quizzes',
                'roles' => [RoleEnum::STUDENT->value, RoleEnum::TEACHER->value]
            ]
        ],
    ]);
    if ($containerConfigurator->env() === 'test') {
        $containerConfigurator->extension('security', [
            'password_hashers' => [
                PasswordAuthenticatedUserInterface::class => [
                    'algorithm' => 'auto',
                    'cost' => 4,
                    'time_cost' => 3,
                    'memory_cost' => 10,
                ],
            ],
        ]);
    }
};
