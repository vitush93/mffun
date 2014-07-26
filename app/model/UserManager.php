<?php

namespace App\Model;

use App\Model\Entities\User;
use Kdyby\Doctrine\EntityManager;
use Nette,
    Nette\Security\Passwords;


/**
 * Users management.
 */
class UserManager extends Nette\Object implements Nette\Security\IAuthenticator
{
    /** @var EntityManager */
    private $em;

    /** @var \Kdyby\Doctrine\EntityDao */
    private $usersDao;

    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
        $this->usersDao = $entityManager->getDao(User::getClassName());
    }

    public function authenticate(array $credentials)
    {
        list($username, $password) = $credentials;

        $row = $this->usersDao->findOneBy(array(
            'username' => $username
        ));

        if (!$row) {
            throw new Nette\Security\AuthenticationException('The username is incorrect.', self::IDENTITY_NOT_FOUND);
        } elseif (!Passwords::verify($password, $row->getPassword())) {
            throw new Nette\Security\AuthenticationException('The password is incorrect.', self::INVALID_CREDENTIAL);
        } elseif (Passwords::needsRehash($row->getPassword())) {
            $row->update(array(
                'password' => Passwords::hash($password),
            ));
        }

        $arr = array(
            'username' => $row->getUsername(),
            'id' => $row->getId(),
            'role' => $row->getRole()
        );

        return new Nette\Security\Identity($arr['id'], $arr['role'], $arr);
    }

    public function add(array $data)
    {
        $newUser = new User();
        $newUser->setUsername($data['username']);
        $newUser->setPassword($data['password']);
        $newUser->setRole($data['role']);
        $this->em->persist($newUser);
        $this->em->flush();
    }

}
