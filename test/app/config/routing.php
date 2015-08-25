<?php

return [
    'core' => [
        'routing' => [
            'home' => [
                'path'     => '/welcome/{name}',
                'defaults' => [
                    '_controller' => '@app.default_controller::indexAction'
                ]
            ]
        ]
    ]
];