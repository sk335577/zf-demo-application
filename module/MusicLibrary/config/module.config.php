<?php

namespace MusicLibrary;

use Zend\Router\Http\Literal;
use Zend\Router\Http\Segment;
use Zend\ServiceManager\Factory\InvokableFactory;

return [
    'view_manager' => [
        'template_map' => [
            'music-library/layout' => __DIR__ . '/../view/layout/layout.phtml',
            'music-library/partial/paginator' => __DIR__ . '/../view/partial/paginator.phtml',
            'music-library/partial/breadcrumb' => __DIR__ . '/../view/partial/breadcrumb.phtml',
        ],
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
    'navigation' => [
        'default' => [
            [
                'label' => 'Home',
                'route' => 'home',
            ],
            [
                'label' => 'Music Library',
                'route' => 'albums',
                'pages' => [
                    [
                        'label' => 'Add',
                        'route' => 'albums',
                        'action' => 'add',
                    ],
                    [
                        'label' => 'Edit',
                        'route' => 'albums',
                        'action' => 'edit',
                    ],
                    [
                        'label' => 'Delete',
                        'route' => 'albums',
                        'action' => 'delete',
                    ],
                ],
            ],
        ],
    ],
    'router' => [
        'routes' => [
            'albums' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/albums[/:id][/:action]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\AlbumController::class,
                        'action' => 'index',
                    ],
                ],
            ],
        ],
    ],
];
