<?php

namespace App\Model\Repositories;


use App\Model\Entities\User;
use Kdyby\Doctrine\EntityManager;
use Nette\Object;

class UserRepository extends Object
{
    /** @var \Kdyby\Doctrine\EntityManager */
    private $em;

    /** @var \Kdyby\Doctrine\EntityDao */
    private $userDao;

    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
        $this->userDao = $entityManager->getDao(User::getClassName());
    }

    /**
     * @param $username
     * @return mixed|null|User
     */
    public function findOneByUsername($username)
    {
        return $this->userDao->findOneBy(array('username' => $username));
    }

    /**
     * @param integer $id
     * @return User
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
        return $this->userDao->findBy([], array('id' => 'DESC'));
    }

    /**
     * @param integer $id
     * @return void
     */
    public function remove($id)
    {
        $this->userDao->delete($this->userDao->find($id));
    }
} 