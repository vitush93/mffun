<?php

namespace App\Model\Repositories;


use App\Model\Entities\User;
use Kdyby\Doctrine\EntityManager;
use Nette\Object;

class UserRepository extends Object implements IRepository
{
    private $em;
    private $userDao;

    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
        $this->userDao = $entityManager->getDao(User::getClassName());
    }

    public function findOneByUsername($username)
    {
        return $this->userDao->findOneBy(array('username' => $username));
    }

    /**
     * @param integer $id
     * @return mixed
     */
    public function find($id)
    {
        return $this->userDao->find($id);
    }

    /**
     * @return array
     */
    public function findAll()
    {
        // TODO: Implement findAll() method.
    }

    /**
     * @param object|array|\Traversable $entity
     * @return object|array|\Traversable
     */
    public function add($entity)
    {
        return $this->userDao->save($entity);
    }

    /**
     * @param integer $id
     * @return void
     */
    public function remove($id)
    {
        $this->userDao->delete($this->userDao->find($id));
    }

    /**
     * alias for $this->add($entity)
     *
     * @param object|array|\Traversable $entity
     * @return object|array|\Traversable
     */
    public function save($entity)
    {
        return $this->userDao->save($entity);
    }


} 