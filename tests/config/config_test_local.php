<?php

use Phalcon\Config;

return new Config([
    'database' => [
        'adapter' => 'Mysql',
        'host' => '127.0.0.1',
        'username' => 'root',
        'password' => '',
        'dbname' => 'user_auth_db'
    ],
]);