<?php

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

// teachers
foreach ($context->table('mff_teachers') as $teacher) {
    $t = $em->getRepository(\App\Model\Entities\Teacher::class)->findOneBy(['name' => $teacher->fullName]);
    if (!$t) {
        $t = new \App\Model\Entities\Teacher($teacher->fullName);
        $em->persist($t);
    }
    $em->flush();
}


// subjects
foreach ($context->table('mff_subjects') as $subject) {
    $s = $em->getRepository(\App\Model\Entities\Subject::class)->findOneBy(['name' => $subject->fullName]);
    if (!$s) {
        $s = new \App\Model\Entities\Subject($subject->fullName);
        $em->persist($s);
    }
    $em->flush();
}


// quotes
foreach ($context->table('mff_quotes') as $q) {
    $quote = new \App\Model\Entities\Quote($q->quoteText, new DateTime($q->checkDate));
    $quote->setUser($em->getRepository(\App\Model\Entities\User::class)->findOneBy(['username' => 'unknown']));
    $quote->approve();
    $quote->setUserEmail($q->senderEmail);

    $tname = $context->table('mff_teachers')->get($q->teacherId);
    if ($tname) {
        $tname = $tname->fullName;
        $quote->setTeacher($em->getRepository(\App\Model\Entities\Teacher::class)->findOneBy(['name' => $tname]));
    }


    $sname = $context->table('mff_subjects')->get($q->subjectId);
    if ($sname) {
        $sname = $sname->fullName;
        $quote->setSubject($em->getRepository(\App\Model\Entities\Subject::class)->findOneBy(['name' => $sname]));
    }

    $em->persist($quote);
}

$em->flush();
