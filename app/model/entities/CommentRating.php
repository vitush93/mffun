<?php


namespace App\Model\Entities;


use App\Libs\IRating;
use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\BaseEntity;
use Nette\Utils\DateTime;

/**
 * @ORM\Entity
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="rating_unique", columns={"comment_id", "user_id"})})
 * @ORM\EntityListeners({"App\Model\Events\CommentRatingListener"})
 */
class CommentRating extends BaseEntity implements IRating, \JsonSerializable
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

    /**
     * @param User $user
     * @param Comment $comment
     */
    public function __construct(User $user, Comment $comment)
    {
        $this->setUser($user);
        $this->setComment($comment);
        $this->rated = new \DateTime();
    }

    /**
     * @param Comment $comment
     */
    public function setComment($comment)
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

    public function setPositive()
    {
        $this->value = CommentRating::POSITIVE;
    }

    public function setNegative()
    {
        $this->value = CommentRating::NEGATIVE;
    }

    public function isNegative()
    {
        return ($this->value == CommentRating::NEGATIVE);
    }

    public function isPositive()
    {
        return ($this->value == CommentRating::POSITIVE);
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

    function jsonSerialize()
    {
        return array(
            'c_id' => $this->comment->getId(),
            'u_id' => $this->user->getId(),
            'value' => $this->value
        );
    }

} 