<?php

require_once '../vendor/autoload.php';

$container = require __DIR__ . '/../app/bootstrap.php';

/** @var \Kdyby\Doctrine\EntityManager $em */
$em = $container->getByType('Kdyby\Doctrine\EntityManager');

$tags = [
    'praktika', 'prednaska', 'cviceni', 'leto', 'zima', 'informatika', 'matika', 'fyzika', 'studenti', 'hlaska', 'citace', 'moudro', 'jazyk', 'test', 'zkouska', 'zapocet', 'lab'
];

foreach ($tags as $t) {
    $tag = new \App\Model\Entities\Tag($t);
    $em->persist($tag);
}
$em->flush();


/** @var \App\Model\Entities\Quote $t */
foreach ($em->getRepository(\App\Model\Entities\Quote::class)->findAll() as $q) {
    $x = [];
    foreach (array_rand($tags, rand(2, 5)) as $r) {
        $x[] = $tags[$r];
    }
    $tg = $em->getRepository(\App\Model\Entities\Tag::class)->findBy(['tag' => $x]);
    /** @var \App\Model\Entities\Tag $t */
    foreach ($tg as $t) {
        $t->assignToQuote($q);
    }
}
$em->flush();


