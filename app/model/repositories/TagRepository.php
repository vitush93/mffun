<?php

namespace App\Model\Repositories;

use App\Model\Entities\Tag;
use Kdyby\Doctrine\EntityManager;
use Nette\Object;

class TagRepository extends Object implements IRepository
{
    use RepositoryTrait;

    /** @var \Kdyby\Doctrine\EntityDao */
    private $tagDao;

    /** @var EntityManager */
    private $em;

    public function __construct(EntityManager $entityManager)
    {
        $this->tagDao = $entityManager->getDao(Tag::getClassName());
        $this->em = $entityManager;
    }

    public function findOneByTag($tag)
    {
        return $this->tagDao->findOneBy(array('tag' => $tag));
    }

    /**
     * @param integer $id
     * @return mixed
     */
    public function find($id)
    {
        // TODO: Implement find() method.
    }

    /**
     * @return array
     */
    public function findAll()
    {
        // TODO: Implement findAll() method.
    }

    /**
     * @param integer $id
     * @return void
     */
    public function remove($id)
    {
        // TODO: Implement remove() method.
    }


} 