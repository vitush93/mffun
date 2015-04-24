<?php

// TODO

require_once '../vendor/autoload.php';

\Tracy\Debugger::enable();

$dsn = 'mysql:host=localhost;dbname=mff';
$user = 'root';
$pass = '';

$connection = new \Nette\Database\Connection($dsn, $user, $pass);
$structure = new \Nette\Database\Structure($connection, new \Nette\Caching\Storages\FileStorage(__DIR__ . '/../temp'));
$context = new \Nette\Database\Context($connection, $structure);

$container = require __DIR__ . '/../app/bootstrap.php';
/** @var \Kdyby\Doctrine\EntityManager $em */
$em = $container->getByType('Kdyby\Doctrine\EntityManager');

foreach ($context->table('mff_quotes') as $q) {
    $quote = new \App\Model\Entities\Quote();
    $quote->setDate($q->quoteDate);
    $quote->setUser($em->getRepository(\App\Model\Entities\User::class)->findOneBy(['username' => 'unknown']));
    $quote->approve();
    $quote->setText($q->quoteText);

    $em->persist($quote);
}

$em->flush();
