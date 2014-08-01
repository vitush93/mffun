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
     */
    private $quotations;

    /**
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="user")
     */
    private $comments;

    /**
     * @ORM\OneToMany(targetEntity="QuoteRating", mappedBy="user")
     */
    private $quote_ratings;

    /**
     * @ORM\OneToMany(targetEntity="CommentRating", mappedBy="user")
     */
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
    private $role = self::ROLE_USER;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @var string
     */
    private $name;

    /**
     * @ORM\Column(type="datetime")
     * @var DateTime
     */
    private $registered;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @var string
     */
    private $avatar;

    /**
     * @ORM\Column(type="boolean")
     * @var int
     */
    private $active = TRUE;

    public function __construct()
    {
        $this->registered = new DateTime();
        $this->quotations = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->comment_ratings = new ArrayCollection();
        $this->quote_ratings = new ArrayCollection();
    }

    /**
     * @param CommentRating $commentRating
     */
    public function addCommentRating($commentRating)
    {
        $this->comment_ratings[] = $commentRating;
    }

    /**
     * @param Quote $quote
     */
    public function addQuote($quote)
    {
        $this->quotations[] = $quote;
    }

    /**
     * @param QuoteRating $quoteRating
     */
    public function addQuoteRating($quoteRating)
    {
        $this->quote_ratings[] = $quoteRating;
    }

    /**
     * @param Comment $comment
     */
    public function addComment($comment)
    {
        $this->comments[] = $comment;
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
        if($password === NULL) {
            $this->password = NULL;
        }else{
            $this->password = Passwords::hash($password);
        }
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
        if (!in_array($role, self::$ALLOWED_ROLES)) {
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

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return ArrayCollection
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * @param string $avatar
     */
    public function setAvatar($avatar)
    {
        $this->avatar = $avatar;
    }

    /**
     * @return string
     */
    public function getAvatar()
    {
        return $this->avatar;
    }

    /**
     * @return \DateTime
     */
    public function getRegistered()
    {
        return $this->registered;
    }

    /**
     * @return ArrayCollection
     */
    public function getQuoteRatings()
    {
        return $this->quote_ratings;
    }

    /**
     * @return ArrayCollection
     */
    public function getQuotations()
    {
        return $this->quotations;
    }

    /**
     * @param int $active
     */
    public function setActive($active)
    {
        $this->active = $active;
    }

    /**
     * @return int
     */
    public function getActive()
    {
        return $this->active;
    }



} 