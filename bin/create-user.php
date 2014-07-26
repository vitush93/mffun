<?php

if (!isset($_SERVER['argv'][3])) {
    echo '
Add new user to database.

Usage: create-user.php <name> <password> <role> <email>
';
    exit(1);
}

$data = array(
    'username' => $_SERVER['argv'][1],
    'password' => $_SERVER['argv'][2],
    'role' => $_SERVER['argv'][3],
    'email' => $_SERVER['argv'][4],
);

$container = require __DIR__ . '/../app/bootstrap.php';
$container->getByType('App\Model\UserManager')->add($data);

echo "User {$_SERVER['argv'][1]} was added.\n";