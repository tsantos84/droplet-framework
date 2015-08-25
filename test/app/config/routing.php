<?php

return [
    'core' => [
        'routing' => [
            'home' => [
                'path'     => '/',
                'defaults' => [
                    '_controller' => 'App\Controller\DefaultController::indexAction'
                ]
            ]
        ]
    ]
];