<?php

namespace App\Model\Entities;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\BaseEntity;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use DateTime;

/**
 * @ORM\Entity
 * @ORM\Table(name="comments")
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

    public function __construct()
    {
        $this->posted = new DateTime();
        $this->ratings = new ArrayCollection();
    }

    /**
     * @param CommentRating $commentRating
     */
    public function addRating(CommentRating $commentRating)
    {
        $this->ratings[] = $commentRating;
    }

    /**
     * @param \App\Model\Entities\Quote $quote
     */
    public function setQuote(Quote $quote)
    {
        $quote->addComment($this);
        $this->quote = $quote;
    }

    /**
     * @return \App\Model\Entities\Quote
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
     * @param \App\Model\Entities\User $user
     */
    public function setUser(User $user)
    {
        $user->addComment($this);
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


}