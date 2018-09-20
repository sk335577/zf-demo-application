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
            'admin/partial/header' => __DIR__ . '/../view/partial/header.phtml',
            'admin/partial/sidebar-left' => __DIR__ . '/../view/partial/sidebar-left.phtml',
            'admin/partial/footer' => __DIR__ . '/../view/partial/footer.phtml',
            'admin/partial/control-sidebar' => __DIR__ . '/../view/partial/control-sidebar.phtml',
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
