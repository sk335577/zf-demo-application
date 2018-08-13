<?php

namespace MusicLibrary;

use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\ModuleManager\Feature\ConfigProviderInterface;

class Module implements ConfigProviderInterface {

    const VERSION = '3.0.3-dev';

    public function getConfig() {
        return include __DIR__ . '/../config/module.config.php';
    }

    public function getControllerConfig() {
        return [
            'factories' => [
                Controller\AlbumController::class => function($container) {
                    return new Controller\AlbumController(
                            $container->get(Model\AlbumTable::class)
                    );
                },
                Controller\SongController::class => function($container) {
                    return new Controller\SongController(
                            $container->get(Model\SongTable::class)
                    );
                },
            ],
        ];
    }

    public function getServiceConfig() {
        return [
            'factories' => [
                Model\AlbumTable::class => function($container) {
                    $tableGateway = $container->get(Model\AlbumTableGateway::class);
                    return new Model\AlbumTable($tableGateway);
                },
                Model\AlbumTableGateway::class => function ($container) {
                    $dbAdapter = $container->get(AdapterInterface::class);
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Model\Album());
                    return new TableGateway('albums', $dbAdapter, null, $resultSetPrototype);
                },
                Model\SongTable::class => function($container) {
                    $tableGateway = $container->get(Model\SongTableGateway::class);
                    return new Model\SongTable($tableGateway);
                },
                Model\SongTableGateway::class => function ($container) {
                    $dbAdapter = $container->get(AdapterInterface::class);
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Model\Song());
                    return new TableGateway('songs', $dbAdapter, null, $resultSetPrototype);
                },
            ],
        ];
    }

}
