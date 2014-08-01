<?php

namespace App\Model\Repositories;

use App\Model\Entities\Subject;
use Kdyby\Doctrine\EntityManager;
use Kdyby\Doctrine\EntityDao;
use Nette\Object;

class SubjectRepository extends Object implements IRepository
{
    use RepositoryTrait;

    /** @var EntityDao */
    private $subjectDao;

    /** @var EntityManager */
    private $em;

    public function __construct(EntityManager $entityManager)
    {
        $this->subjectDao = $entityManager->getDao(Subject::getClassName());
        $this->em = $entityManager;
    }

    public function findOneByName($subject)
    {
        return $this->subjectDao->findOneBy(array('name' => $subject));
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