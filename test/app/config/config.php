<?php

return [

    '@import' => [
        'parameters.php',
        'doctrine.php',
        'routing.php'
    ],

    'templating' => [
        'paths' => [
            __DIR__ . '/../views'
        ]
    ]
];