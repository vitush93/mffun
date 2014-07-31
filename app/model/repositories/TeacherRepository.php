<?php

namespace App\Model\Repositories;


use App\Model\Entities\Teacher;
use Kdyby\Doctrine\EntityManager;
use Nette\Object;

class TeacherRepository extends Object implements IRepository
{
    private $em;
    private $teacherDao;

    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
        $this->teacherDao = $entityManager->getDao(Teacher::getClassName());
    }

    /**
     * @param $name string
     * @return mixed|null|object
     */
    public function findOneByName($name)
    {
        return $this->teacherDao->findOneBy(array('name' => $name));
    }

    /**
     * @param array|object|\Traversable $teacher
     * @return array|object|\Traversable
     */
    public function add($teacher)
    {
        return $this->teacherDao->save($teacher);
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