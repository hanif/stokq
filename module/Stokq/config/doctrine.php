<?php

return [
    'configuration' => [
        'orm_default' => [
            'entity_namespaces' => [
                '' => 'Stokq\Entity',
                'c' => 'Stokq\Entity',
                'core' => 'Stokq\Entity',
                'ent' => 'Stokq\Entity',
                'dto' => 'Stokq\DTO',
            ],
        ],
    ],
    'driver' => [
        'annotation' => [
            'paths' => [
                realpath(__DIR__ . '/../src/Stokq/Entity'),
            ],
        ],
        'orm_default' => [
            'drivers' => [
                'Stokq\Entity' => 'annotation',
            ],
        ],
    ],
];