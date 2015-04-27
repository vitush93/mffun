<?php

namespace App\Model\Entities;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\BaseEntity;

/**
 * @ORM\Entity
 */
class Comment extends BaseEntity
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
        $this->ratings[] = $commentRating;
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


}