<?php
use Phalcon\Di;
use Phalcon\Di\FactoryDefault;
use Phalcon\Loader;

ini_set('display_startup_errors', '1');
ini_set('display_errors',1);
error_reporting(E_ALL);

define('ROOT_PATH', __DIR__);

set_include_path(
    ROOT_PATH . PATH_SEPARATOR . get_include_path()
);

$env = getenv('APPLICATION_ENVIRONMENT');

// Required for phalcon/incubator
include __DIR__ . "/../vendor/autoload.php";

/**
 * Read the configuration
 */
if (($env = getenv('APPLICATION_ENV')) == false) {
    $config = include ROOT_PATH . "/config/config_test_local.php";
} else {
    $config = include __DIR__ . "/../app/config/config_test_$env.php";
}

// Use the application autoloader to autoload the classes
include __DIR__ . "/config/loader.php";

//include application services
include __DIR__ . "/config/services.php";

Di::reset();

Di::setDefault($di);