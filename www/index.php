<?php

function dirExists($path)
{
    if (!file_exists($path) && !is_dir($path)) {
        return false;
    } else {
        return true;
    }
}

function buildDir($path)
{
    if (!dirExists($path)) {
        if (!mkdir($path)) {
            trigger_error('could not create ' . $path . ' folder, create it manually');
        }
    }
}


buildDir('../temp');
buildDir('../log');
buildDir('upload');

// Uncomment this line if you must temporarily take down your site for maintenance.
// require '.maintenance.php';

define('WWW_DIR', __DIR__);

$container = require __DIR__ . '/../app/bootstrap.php';

$container->getService('application')->run();
