<?php

return [
    'routing' => [
        'home' => [
            'path'     => '/welcome/{name}',
            'defaults' => [
                '_controller' => '@app.default_controller::indexAction'
            ]
        ]
    ]
];