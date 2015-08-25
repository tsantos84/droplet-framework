<?php

return [
    'doctrine' => [
        'orm' => [
            'mapping' => [
                'path'   => [
                    __DIR__ . '/../../src/App/Entity'
                ],
                'loader' => 'annotation'
            ],
            'manager' => [
                'default' => [
                    'driver' => 'pdo_sqlite',
                    'path'   => __DIR__ . '/../data'
                ]
            ]
        ]
    ]
];