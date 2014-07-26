<?php

namespace App\Model;

use App\Model\Entities\Article;
use Kdyby\Doctrine\EntityManager;
use Nette\Object;

class Articles extends Object
{
    private $entityManager;
    private $articlesDao;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->articlesDao = $entityManager->getDao(Article::getClassName());
    }
    
    public function findAll()
    {
        return $this->articlesDao->findAll();
    }

}