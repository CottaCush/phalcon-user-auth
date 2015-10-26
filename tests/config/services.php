<?php

use Phalcon\Di\FactoryDefault;
use Phalcon\Db\Adapter\Pdo\Mysql as DbAdapter;
use Phalcon\Mvc\Model\Metadata\Memory as MetaData;

$di = new FactoryDefault();

/**
 * Add Db Service
 */
$di->set('db', function () use ($config) {
    return new DbAdapter([
        'host' => $config->database->host,
        'username' => $config->database->username,
        'password' => $config->database->password,
        'dbname' => $config->database->dbname
    ]);
});

/**
 * If the configuration specify the use of metadata adapter use it or use memory otherwise
 */
$di->set('modelsMetadata', function () {
    return new MetaData();
});

/**
 * Add models manager
 */
$di->setShared('modelsManager', function () {
    return new Phalcon\Mvc\Model\Manager();
});

/**
 * Add security
 */
$di->setShared('security', function () {
    $security = new \Phalcon\Security();
    $security->setWorkFactor(12);
    return $security;
});

/**
 * Add config
 */
$di->set('config', $config);

