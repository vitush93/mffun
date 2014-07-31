<?php

// create required folders
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
buildDir('../www/upload');
buildDir('../www/temp');

// add dummy user
$container = require __DIR__ . '/../app/bootstrap.php';

$dummy_user = new \App\Model\Entities\User();
$dummy_user->setUsername('unknown');
$dummy_user->setPassword('123');
$dummy_user->setRole('user');
$dummy_user->setEmail('unk@unk.com');
$dummy_user->setActive(false);

$em = $container->getByType('Kdyby\Doctrine\EntityManager');

$em->persist($dummy_user);
$em->flush();

