<?php

return [
    'core' => [
        'routing' => [
            'home' => [
                'path'     => '/',
                'defaults' => [
                    '_controller' => '@app.default_controller::indexAction'
                ]
            ]
        ]
    ]
];