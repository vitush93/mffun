<?php

namespace App\Model\Services;

use App\Model\Entities\User;
use Kdyby\Doctrine\EntityManager;
use Nette;
use Nette\Object;
use Nette\Security\Passwords;

class AuthenticationService extends Object implements Nette\Security\IAuthenticator
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

    /**
     * Try to authenticate the user.
     *
     * @param array $credentials
     * @return Nette\Security\Identity|Nette\Security\IIdentity
     * @throws Nette\Security\AuthenticationException
     */
    public function authenticate(array $credentials)
    {
        list($username, $password) = $credentials;

        /** @var User $row */
        $row = $this->usersDao->findOneBy(array(
            'username' => $username,
            'active' => TRUE
        ));

        if (!$row) {
            throw new Nette\Security\AuthenticationException('Špatný login.', self::IDENTITY_NOT_FOUND);
        } elseif (!Passwords::verify($password, $row->getPassword())) {
            throw new Nette\Security\AuthenticationException('Špatné heslo.', self::INVALID_CREDENTIAL);
        } elseif (Passwords::needsRehash($row->getPassword())) {
            $row->update(array(
                'password' => Passwords::hash($password),
            ));
        } elseif ($row->isBanned()) {
            throw new Nette\Security\AuthenticationException('Byl jsi zabanován. Napiš adminovi na ' . $row->getBan() . '.', self::NOT_APPROVED);
        }

        $arr = array(
            'username' => $row->getUsername(),
            'id' => $row->getId(),
            'role' => $row->getRole()
        );

        return new Nette\Security\Identity($arr['id'], $arr['role'], $arr);
    }
}