<?php

namespace Admin;

use Zend\Router\Http\Literal;
use Zend\Router\Http\Segment;
use Zend\ServiceManager\Factory\InvokableFactory;

return [
    'controllers' => [
        'factories' => [
            Controller\IndexController::class => InvokableFactory::class,
        ],
    ],
    'view_manager' => [
        'template_map' => [
            'admin/layout' => __DIR__ . '/../view/layout/layout.phtml',
            'admin/layout/login' => __DIR__ . '/../view/layout/layout_login.phtml',
        ],
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
    'router' => [
        'routes' => [
            'admin' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/admin[/[:action]]',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action' => 'index',
                    ],
                ],
            ],
        ],
    ],
];
