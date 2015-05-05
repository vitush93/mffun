<?php

namespace App\Model\Repositories;


use App\Model\Entities\User;
use Kdyby\Doctrine\EntityManager;
use Nette\Object;
use Nette\Utils\ArrayHash;

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
     * [RegisterForm]
     * Creates a new user from submitted data.
     *
     * @param ArrayHash $values
     * @param int $crank
     * @param bool $auth
     */
    public function createUser(ArrayHash $values, $crank = 0, $auth = NULL)
    {
        $user = new User();

        $user->setUsername($values->username);
        $user->setEmail($values->email);
        $user->setName($values->name);
        $user->setPassword($values->password);
        $user->setCrank($crank);
        if ($auth) {
            $user->setMff($auth);
        }

        $this->add($user);
    }

    /**
     * @param User $user
     */
    public function add(User $user)
    {
        $this->em->persist($user);
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