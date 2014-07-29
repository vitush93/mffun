<?php


namespace App\Model\Entities;


use Kdyby\Doctrine\Entities\BaseEntity;
use Doctrine\ORM\Mapping as ORM;
use Nette\Utils\DateTime;
use Doctrine\ORM\Mapping\ManyToOne;

/**
 * @ORM\Entity
 * @ORM\Table(name="comment_ratings")
 */
class CommentRating extends BaseEntity
{
    const
        POSITIVE = 1,
        NEGATIVE = -1;
    public static $ALLOWED_RATINGS = array(self::POSITIVE, self::NEGATIVE);

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     * @var integer
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="comment_ratings")
     * @var User
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="Comment", inversedBy="ratings")
     * @var Comment
     */
    private $comment;

    /**
     * @ORM\Column(type="datetime")
     * @var DateTime
     */
    private $rated;

    /**
     * @ORM\Column(type="integer")
     * @var integer
     */
    private $value;

    public function __construct()
    {
        $this->rated = new \DateTime();
    }

    /**
     * @param \App\Model\Entities\Comment $comment
     */
    public function setComment(Comment $comment)
    {
        $comment->addRating($this);
        $this->comment = $comment;
    }

    /**
     * @return \App\Model\Entities\Comment
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param \Nette\Utils\DateTime $rated
     */
    public function setRated($rated)
    {
        $this->rated = $rated;
    }

    /**
     * @return \Nette\Utils\DateTime
     */
    public function getRated()
    {
        return $this->rated;
    }

    /**
     * @param \App\Model\Entities\User $user
     */
    public function setUser($user)
    {
        $user->addCommentRating($this);
        $this->user = $user;
    }

    /**
     * @return \App\Model\Entities\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param $value
     * @throws \InvalidArgumentException
     */
    public function setValue($value)
    {
        if (!in_array($value, self::$ALLOWED_RATINGS)) {
            throw new \InvalidArgumentException("Invalid rating value: $value");
        }
        $this->value = $value;
    }

    /**
     * @return int
     */
    public function getValue()
    {
        return $this->value;
    }



} 