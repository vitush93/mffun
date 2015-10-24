<?php

namespace App\Model\Entities;

use App\Libs\IRateable;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\BaseEntity;
use Kdyby\Doctrine\NotImplementedException;
use Nette\InvalidArgumentException;

/**
 * @ORM\Entity
 */
class Comment extends BaseEntity implements IRateable, \JsonSerializable
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     * @var integer
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="comments")
     * @var User
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="Quote", inversedBy="comments")
     * @var Quote
     */
    private $quote;

    /**
     * @ORM\OneToMany(targetEntity="CommentRating", mappedBy="comment")
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $ratings;

    /**
     * @ORM\Column(type="text")
     * @var string
     */
    private $text;

    /**
     * @ORM\Column(type="datetime")
     * @var DateTime
     */
    private $posted;

    /**
     * @ORM\Column(type="integer")
     * @var int
     */
    private $parent = 0;

    /**
     * @ORM\Column(type="integer")
     * @var integer
     */
    private $ratingUp = 0;

    /**
     * @ORM\Column(type="integer")
     * @var integer
     */
    private $ratingDown = 0;

    public function __construct()
    {
        $this->posted = new DateTime();
        $this->ratings = new ArrayCollection();
    }

    /**
     * @param CommentRating $commentRating
     */
    public function addRating($commentRating)
    {
        $this->ratings->add($commentRating);
    }

    /**
     * @param Quote $quote
     */
    public function setQuote($quote)
    {
        $quote->addComment($this);
        $this->quote = $quote;
    }

    /**
     * @return Quote
     */
    public function getQuote()
    {
        return $this->quote;
    }

    /**
     * @return ArrayCollection
     */
    public function getRatings()
    {
        return $this->ratings;
    }

    /**
     * @param string $text
     */
    public function setText($text)
    {
        if (strlen($text) == 0) throw new InvalidArgumentException('Comment text must be a non-empty string.');

        $this->text = $text;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param User $user
     */
    public function setUser($user)
    {
        $user->addComment($this);
        $this->user = $user;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return \DateTime
     */
    public function getPosted()
    {
        return $this->posted;
    }

    /**
     * @param int $parent
     */
    public function setParent($parent)
    {
        $this->parent = $parent;
    }

    /**
     * @return int
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $ratingDown
     */
    public function setRatingDown($ratingDown)
    {
        $this->ratingDown = $ratingDown;
    }

    /**
     * @return int
     */
    public function getRatingDown()
    {
        return $this->ratingDown;
    }

    /**
     * @param int $ratingUp
     */
    public function setRatingUp($ratingUp)
    {
        $this->ratingUp = $ratingUp;
    }

    /**
     * @return int
     */
    public function getRatingUp()
    {
        return $this->ratingUp;
    }

    public function getDiff()
    {
        $now = new DateTime();
        $diff = $now->diff($this->posted);

        if ($diff->d > 0) {
            if ($diff->d < 3) {
                return $diff->d . 'd';
            } else {
                return $this->posted->format('j.n.Y H:i');
            }
        } else if ($diff->h > 0) {
            return $diff->h . 'h ' . $diff->i . 'm';
        } else if ($diff->i > 0) {
            return $diff->i . 'm ' . $diff->s . 's';
        } else {
            if ($diff->s < 20) {
                return "právě teď";
            } else {
                return $diff->s . 's';
            }
        }
    }

    /**
     * Returns numeric representation of item's rating.
     *
     * @return number
     */
    function getRating()
    {
        throw new NotImplementedException;
    }

    /**
     * Sets calculated numeric item's rating value.
     *
     * @param $value
     * @return mixed
     */
    function setRating($value)
    {
        throw new NotImplementedException;
    }

    function jsonSerialize()
    {
        return array(
            'id' => $this->id,
            'parent' => $this->parent,
            'text' => $this->text,
            'user' => $this->user,
            'up' => $this->ratingUp,
            'down' => $this->ratingDown,
            'diff' => $this->getDiff(),
            'admin' => ($this->user->getRole() == User::ROLE_ADMIN),
            'mod' => ($this->user->getRole() == User::ROLE_MODERATOR),
            'mff' => ($this->user->isMff())
        );
    }


}