<?php

$composer_file = dirname(__DIR__) . '/composer.json';

if (!isset($argv[1])) {
    echo "version not supplied\n";
    exit(1);
}

$version = $argv[1];
$composer_config = file_get_contents($composer_file);

if (!$composer_config) {
    echo "could not find composer.json file\n";
    exit(1);
}

$composer_config = preg_replace('/"version".*\:.*,/', '"version" : "' . $version . '",', $composer_config);

if (file_put_contents($composer_file, $composer_config)) {
    echo "bumped composer version to " . $version . "\n";
    exit(0);
} else {
    echo "could not bump composer version to " . $version . "\n";
    exit(1);
}

