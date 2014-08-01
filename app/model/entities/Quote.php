<?php

namespace App\Model\Entities;


use Doctrine\Common\Collections\ArrayCollection;
use Kdyby\Doctrine\Entities\BaseEntity;
use Doctrine\ORM\Mapping as ORM;
use DateTime;
use Doctrine\ORM\Mapping\ManyToOne;

/**
 * @ORM\Entity
 * @ORM\Table(name="quotations")
 */
class Quote extends BaseEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     * @var integer
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="quotations")
     * @var User
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="Teacher", inversedBy="quotations")
     * @var Teacher
     */
    private $teacher;

    /**
     * @ORM\ManyToOne(targetEntity="Subject", inversedBy="quotations")
     * @var Subject
     */
    private $subject;

    /**
     * @ORM\ManyToMany(targetEntity="Tag", mappedBy="quotations")
     * @var ArrayCollection
     */
    private $tags;

    /**
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="quote")
     * @ORM\OrderBy({"rating_up" = "DESC"})
     * @var ArrayCollection
     */
    private $comments;

    /**
     * @ORM\OneToMany(targetEntity="QuoteRating", mappedBy="quote")
     * @var ArrayCollection
     */
    private $ratings;

    /**
     * @ORM\Column(type="datetime")
     * @var DateTime
     */
    private $date;

    /**
     * @ORM\Column(type="datetime")
     * @var DateTime
     */
    private $posted;

    /**
     * @ORM\Column(type="text")
     * @var string
     */
    private $text;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @var string
     */
    private $user_email;

    /**
     * @ORM\Column(type="float")
     * @var float
     */
    private $rating = 0;

    public function __construct()
    {
        $this->posted = new DateTime();
        $this->comments = new ArrayCollection();
        $this->ratings = new ArrayCollection();
        $this->tags = new ArrayCollection();
    }

    /**
     * @param QuoteRating $quoteRating
     */
    public function addRating($quoteRating)
    {
        $this->ratings[] = $quoteRating;
    }

    /**
     * @param Comment $comment
     */
    public function addComment($comment)
    {
        $this->comments[] = $comment;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param \DateTime $date
     */
    public function setDate(DateTime $date)
    {
        $this->date = $date;
    }

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @return \DateTime
     */
    public function getPosted()
    {
        return $this->posted;
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
     * @param string $user_email
     */
    public function setUserEmail($user_email)
    {
        $this->user_email = $user_email;
    }

    /**
     * @return string
     */
    public function getUserEmail()
    {
        return $this->user_email;
    }

    /**
     * @return ArrayCollection
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * @return ArrayCollection
     */
    public function getRatings()
    {
        return $this->ratings;
    }

    /**
     * @param User $user
     */
    public function setUser($user)
    {
        $user->addQuote($this);
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
     * @param Subject $subject
     */
    public function setSubject($subject)
    {
        $subject->addQuote($this);
        $this->subject = $subject;
    }

    /**
     * @return Subject
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @param Teacher $teacher
     */
    public function setTeacher($teacher)
    {
        $teacher->addQuote($this);
        $this->teacher = $teacher;
    }

    /**
     * @return Teacher
     */
    public function getTeacher()
    {
        return $this->teacher;
    }

    /**
     * @param float $rating
     */
    public function setRating($rating)
    {
        $this->rating = $rating;
    }

    /**
     * @return float
     */
    public function getRating()
    {
        return $this->rating*1000;
    }

    /**
     * @return ArrayCollection
     */
    public function getTags()
    {
        return $this->tags;
    }

}