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
                'route' => 'music-library/albums',
                'pages' => [
                    [
                        'label' => 'Add',
                        'route' => 'music-library/albums',
                        'action' => 'add',
                    ],
                    [
                        'label' => 'Edit',
                        'route' => 'music-library/albums',
                        'action' => 'edit',
                    ],
                    [
                        'label' => 'Delete',
                        'route' => 'music-library/albums',
                        'action' => 'delete',
                    ],
                ],
            ],
        ],
    ],
    'router' => [
        'routes' => [
            'music-library' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/music-library',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\HomeController::class,
                        'action' => 'home',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'albums' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/albums[/:action[/:id]]',
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
                    'songs' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/songs[/:action[/:id]]',
                            'constraints' => [
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ],
                            'defaults' => [
                                'controller' => Controller\SongController::class,
                                'action' => 'index',
                            ],
                        ],
                    ],
                ]
            ],
        ],
    ],
];
