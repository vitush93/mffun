<?php

if (!isset($_SERVER['argv'][3])) {
    echo '
Add new user to database.

Usage: create-user.php <name> <password> <role> <email>
';
    exit(1);
}

$container = require __DIR__ . '/../app/bootstrap.php';

$user = new \App\Model\Entities\User();
$user->setUsername($_SERVER['argv'][1]);
$user->setPassword($_SERVER['argv'][2]);
$user->setRole($_SERVER['argv'][3]);
$user->setEmail($_SERVER['argv'][4]);

$em = $container->getByType('Kdyby\Doctrine\EntityManager');

$em->persist($user);
$em->flush();

echo "User {$_SERVER['argv'][1]} was added.\n";