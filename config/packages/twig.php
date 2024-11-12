<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('twig', [
        'file_name_pattern' => '*.twig',
        'form_themes' => [
            'bootstrap_5_layout.html.twig',
            'form/fields/selection_group.html.twig',
            'form/fields/entity_selection_group.html.twig',
            'form/fields/custom_enum_group.html.twig',
            'form/fields/custom_checkbox.html.twig',
        ]
    ]);
    if ($containerConfigurator->env() === 'test') {
        $containerConfigurator->extension('twig', [
            'strict_variables' => true,
        ]);
    }
};
