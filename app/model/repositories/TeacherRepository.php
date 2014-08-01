<?php

namespace App\Model\Repositories;


use App\Model\Entities\Teacher;
use Kdyby\Doctrine\EntityManager;
use Nette\Object;

class TeacherRepository extends Object implements IRepository
{
    use RepositoryTrait;

    /** @var \Kdyby\Doctrine\EntityDao */
    private $teacherDao;

    /** @var \Kdyby\Doctrine\EntityManager */
    private $em;

    function __construct(EntityManager $entityManager)
    {
        $this->teacherDao = $entityManager->getDao(Teacher::getClassName());
        $this->em = $entityManager;
    }

    /**
     * @param string $teacher
     * @return mixed|null|object
     */
    public function findOneByName($teacher)
    {
        return $this->teacherDao->findOneBy(array('name' => $teacher));
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