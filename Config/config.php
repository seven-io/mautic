<?php declare(strict_types=1);

return [
    'author' => 'seven communications GmbH & Co. KG',
    'description' => 'Enables integrations seven Transport',
    'menu' => [
        'main' => [
            'items' => [
                'mautic.sms.smses' => [
                    'access' => ['sms:smses:viewown', 'sms:smses:viewother'],
                    'checks' => ['integration' => ['Seven' => ['enabled' => true]]],
                    'parent' => 'mautic.core.channels',
                    'priority' => 70,
                    'route' => 'mautic_sms_index',
                ],
            ],
        ],
    ],
    'name' => 'Seven',
    'parameters' => [],
    'routes' => [
        'api' => [],
        'main' => [],
        'public' => [],
    ],
    'services' => [
        'events' => [],
        'forms' => [
            'mautic.seven.form.config_auth' => [
                'arguments' => [],
                'class' => \MauticPlugin\MauticSevenBundle\Form\Type\ConfigAuthType::class,
            ],
        ],
        'helpers' => [],
        'integrations' => [
            'mautic.integration.seven' => [
                'arguments' => [],
                'class' => \MauticPlugin\MauticSevenBundle\Integration\SevenIntegration::class,
                'tags' => [
                    'mautic.integration',
                    'mautic.basic_integration',
                    'mautic.config_integration',
                    'mautic.auth_integration',
                ],
            ],
        ],
        'models' => [],
        'other' => [
            'mautic.sms.transport.seven' => [
                'alias' => 'mautic.sms.config.transport.seven',
                'arguments' => [
                    'mautic.integrations.helper',
                    'monolog.logger.mautic',
                    'mautic.lead.model.dnc',
                ],
                'class' => \MauticPlugin\MauticSevenBundle\Transport\SevenTransport::class,
                'tag' => 'mautic.sms_transport',
                'tagArguments' => ['integrationAlias' => 'Seven'],
            ],
        ],
    ],
    'version' => '0.0.3',
];
