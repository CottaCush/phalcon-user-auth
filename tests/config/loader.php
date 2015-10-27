<?php

use Phalcon\Loader;

$loader = new \Phalcon\Loader();

$loader->registerDirs(
    array(
        ROOT_PATH
    )
);

$loader->register();