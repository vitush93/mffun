<?php

namespace App\Model\Repositories;


use App\Model\Entities\Subject;
use Kdyby\Doctrine\Entities\BaseEntity;
use Kdyby\Doctrine\EntityManager;
use Nette\Object;

class SubjectRepository extends Object implements IRepository
{
    private $em;
    private $subjectDao;

    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
        $this->subjectDao = $entityManager->getDao(Subject::getClassName());
    }

    /**
     * @param integer $id
     * @return mixed
     */
    public function find($id)
    {
        return $this->subjectDao->find($id);
    }

    /**
     * @return array
     */
    public function findAll()
    {
        // TODO: Implement findAll() method.
    }

    /**
     * @param array|object|\Traversable $entity
     * @return array|object|\Traversable|void
     */
    public function add($entity)
    {
        return $this->subjectDao->save($entity);
    }

    /**
     * @param integer $id
     * @return void
     */
    public function remove($id)
    {
        // TODO: Implement remove() method.
    }

    /**
     * @param string $subject
     * @return mixed|null|object
     */
    public function findOneByName($subject)
    {
        return $this->subjectDao->findOneBy(array('name' => $subject));
    }


} 