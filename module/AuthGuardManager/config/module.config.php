<?php

namespace AuthGuardManager;

use Main\Factory\SessionManagerFactory;
use Zend\Session\SessionManager;

return [
    'circlical' => [
        'user' => [
            'guards' => [
                'ModuleName' => [
                    "controllers" => [
                        \Application\Controller\IndexController::class => [
                            'default' => [], // anyone can access
                        ],
                        \Application\Controller\MemberController::class => [
                            'default' => ['user'], // specific role access
                        ],
                        \Application\Controller\AdminController::class => [
                            'default' => ['admin'],
                            'actions' => [// action-level guards
                                'list' => ['user'], // role 'user' can access 'listAction' on AdminController
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
    'service_manager' => [
        'factories' => [
            SessionManager::class => SessionManagerFactory::class,
            \AuthGuardManager\Storage\AuthStorage::class => \Zend\ServiceManager\Factory\InvokableFactory::class,
            \Zend\Authentication\AuthenticationService::class => \AuthGuardManager\AuthenticationServiceFactory::class,
        ],
    ],
    'session_validators' => [
        \Zend\Session\Validator\RemoteAddr::class,
        \Zend\Session\Validator\HttpUserAgent::class,
    ],
    'session_config' => [
        'remember_me_seconds' => 604800, // one week
        'use_cookies' => true,
        'cookie_lifetime' => 604800, // one week
        'name' => 'your_session_name',
    ],
    'session_storage' => [
        'type' => \Zend\Session\Storage\SessionArrayStorage::class,
    ],
];
