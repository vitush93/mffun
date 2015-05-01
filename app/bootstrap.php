<?php

require __DIR__ . '/../vendor/autoload.php';

$configurator = new Nette\Configurator;

$configurator->enableDebugger(__DIR__ . '/../log');

$configurator->setTempDirectory(__DIR__ . '/../temp');

$configurator->createRobotLoader()
    ->addDirectory(__DIR__)
    ->register();

$configurator->addConfig(__DIR__ . '/config/config.neon');
$configurator->addConfig(__DIR__ . '/config/config.local.neon');
$configurator->addConfig(__DIR__ . '/config/services.neon');
$configurator->addConfig(__DIR__ . '/config/mailer.neon');

$configurator->onCompile[] = function ($configurator, $compiler) {
    $compiler->addExtension('thumbnail', new \Kollarovic\Thumbnail\DI\Extension);
};

$container = $configurator->createContainer();

return $container;
