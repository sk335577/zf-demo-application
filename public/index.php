<?php

use Zend\Mvc\Application;
use Zend\Stdlib\ArrayUtils;

/**
 * This makes our life easier when dealing with paths. Everything is relative
 * to the application root now.
 */
chdir(dirname(__DIR__));

/**
 * Check the environment variable APP_ENV has been set from apache virtual host or .htaccess
 * Set the environment variable to load environment specific settings.
 */
if (empty(getenv('APPLICATION_ENV'))) {
    putenv('APPLICATION_ENV=' . file_get_contents(__DIR__ . '/../config/environment.php'));
}

// Decline static file requests back to the PHP built-in webserver
if (php_sapi_name() === 'cli-server') {
    $path = realpath(__DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
    if (__FILE__ !== $path && is_file($path)) {
        return false;
    }
    unset($path);
}

// Composer autoloading
include __DIR__ . '/../vendor/autoload.php';

if (!class_exists(Application::class)) {
    throw new RuntimeException(
    "Unable to load application.\n"
    . "- Type `composer install` if you are developing locally.\n"
    . "- Type `vagrant ssh -c 'composer install'` if you are using Vagrant.\n"
    . "- Type `docker-compose run zf composer install` if you are using Docker.\n"
    );
}

// Retrieve configuration
$appConfig = [];
if (file_exists(__DIR__ . '/../config/' . getenv('APPLICATION_ENV') . '.config.php')) {
    $appConfig = ArrayUtils::merge($appConfig, require __DIR__ . '/../config/' . getenv('APPLICATION_ENV') . '.config.php');
}


// Run the application!
Application::init($appConfig)->run();
