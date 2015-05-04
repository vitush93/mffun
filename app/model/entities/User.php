<?php


namespace App\Model\Entities;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\BaseEntity;
use Nette\InvalidArgumentException;
use Nette\Security\Passwords;

/**
 * @ORM\Entity
 */
class User extends BaseEntity
{
    const ROLE_ADMIN = 'admin';
    const ROLE_USER = 'user';
    const ROLE_MODERATOR = 'moderator';
    const USER_UNKNOWN = 'unknown';

    public static $ALLOWED_ROLES = array(self::ROLE_ADMIN, self::ROLE_USER, self::ROLE_MODERATOR);

    public static $AVATARS = [
        'bat_128px.png',
        'bear_128px.png',
        'bee_128px.png',
        'bird_128px.png',
        'bug_128px.png',
        'butterfly_128px.png',
        'camel_128px.png',
        'cat_128px.png',
        'cheetah_128px.png',
        'chicken_128px.png',
        'coala_128px.png',
        'cow_128px.png',
        'crocodile_128px.png',
        'dinosaur_128px.png',
        'dog_128px.png',
        'dolphin_128px.png',
        'dove_128px.png',
        'duck_128px.png',
        'eagle_128px.png',
        'elephant_128px.png',
        'fish_128px.png',
        'flamingo_128px.png',
        'fox_128px.png',
        'frog_128px.png',
        'giraffe_128px.png',
        'gorilla_128px.png',
        'horse_128px.png',
        'kangoroo_128px.png',
        'leopard_128px.png',
        'lion_128px.png',
        'monkey_128px.png',
        'mouse_128px.png',
        'panda_128px.png',
        'parrot_128px.png',
        'penguin_128px.png',
        'shark_128px.png',
        'sheep_128px.png',
        'snake_128px.png',
        'spider_128px.png',
        'squirrel_128px.png',
        'star-fish_128px.png',
        'tiger_128px.png',
        'turtle_128px.png',
        'wolf_128px.png',
        'zebra_128px.png',
    ];

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
     * @ORM\OneToMany(targetEntity="QuoteRating", mappedBy="user", cascade={"persist"})
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

    /**
     * @ORM\Column(type="integer")
     * @var int
     */
    private $crank = 5;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @var null|string
     */
    private $ban = NULL;

    public function __construct()
    {
        $this->registered = new DateTime();
        $this->quotations = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->comment_ratings = new ArrayCollection();
        $this->quote_ratings = new ArrayCollection();
        $this->avatar = self::$AVATARS[rand(0, count(self::$AVATARS) - 1)];
    }

    /**
     * @return null|string
     */
    public function getBan()
    {
        return $this->ban;
    }

    /**
     * @param null|string $ban
     */
    public function setBan($ban)
    {
        $this->ban = $ban;
    }

    /**
     * @return bool
     */
    public function isBanned()
    {
        return $this->ban != NULL;
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

    public function increaseCrank()
    {
        $this->crank++;
    }

    public function decreaseCrank()
    {
        $this->crank--;
    }

    /**
     * @return int
     */
    public function getCrank()
    {
        return $this->crank;
    }

    /**
     * @param int $crank
     */
    public function setCrank($crank)
    {
        $this->crank = $crank;
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
        if ($password === NULL) {
            $this->password = NULL;
        } else {
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
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return ArrayCollection
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * @return string
     */
    public function getAvatar()
    {
        return $this->avatar;
    }

    /**
     * @param string $avatar
     */
    public function setAvatar($avatar)
    {
        $this->avatar = $avatar;
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
     * @return int
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * @param int $active
     */
    public function setActive($active)
    {
        $this->active = $active;
    }


}