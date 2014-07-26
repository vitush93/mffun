<?php


namespace App\Model\Entities;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\BaseEntity;
use Nette\InvalidArgumentException;
use Nette\Security\Passwords;
use Doctrine\ORM\Mapping\UniqueConstraint;

/**
 * @ORM\Entity
 * @ORM\Table(name="users",uniqueConstraints={@UniqueConstraint(name="user",columns="username")})
 */
class User extends BaseEntity
{
    public static $ALLOWED_ROLES = array('admin', 'user');

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity="Article",mappedBy="author")
     */
    private $articles;

    /**
     * @ORM\Column(type="string")
     */
    private $username;

    /**
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string")
     */
    private $role;

    public function __construct()
    {
        $this->articles = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function setUsername($username)
    {
        $this->username = $username;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password)
    {
        $this->password = Passwords::hash($password);
    }

    public function getRole()
    {
        return $this->role;
    }

    public function setRole($role)
    {
        if(!in_array($role, self::$ALLOWED_ROLES)){
            throw new InvalidArgumentException("Role {$role} is not in allowed roles.");
        }
        $this->role = $role;
    }

} 