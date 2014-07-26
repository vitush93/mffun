<?php


namespace App\Model\Entities;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\BaseEntity;
use Nette\InvalidArgumentException;
use Nette\Security\Passwords;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Doctrine\ORM\Mapping\OneToMany;
use DateTime;

/**
 * @ORM\Entity
 * @ORM\Table(name="users")
 */
class User extends BaseEntity
{
    const ROLE_ADMIN = 'admin';
    const ROLE_USER = 'user';
    const ROLE_MODERATOR = 'moderator';
    public static $ALLOWED_ROLES = array(self::ROLE_ADMIN, self::ROLE_USER, self::ROLE_MODERATOR);

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     * @var integer
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity="Quote", mappedBy="user")
     * @var Quote
     */
    private $quotations;

    // TODO entities & relationships
    private $comments;
    private $quote_ratings;
    private $comment_ratings;

    /**
     * @ORM\Column(type="string", unique=true)
     * @var string
     */
    private $username;

    /**
     * @ORM\Column(type="string", unique=true)
     * @var string
     */
    private $email;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    private $password;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    private $role;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @var string
     */
    private $name;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @var DateTime
     */
    private $registered;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @var string
     */
    private $avatar;

    public function __construct()
    {
        $this->registered = new DateTime();
        $this->quotations = new ArrayCollection();
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param string $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = Passwords::hash($password);
    }

    /**
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @param string $role
     * @throws \Nette\InvalidArgumentException
     */
    public function setRole($role)
    {
        if(!in_array($role, self::$ALLOWED_ROLES)){
            throw new InvalidArgumentException("Role {$role} is not in allowed roles.");
        }
        $this->role = $role;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

} 