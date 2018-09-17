<?php

namespace Admin;

use Zend\Mvc\MvcEvent;

class Module {

    const VERSION = '3.0.3-dev';

    public function getConfig() {
        return include __DIR__ . '/../config/module.config.php';
    }

}
