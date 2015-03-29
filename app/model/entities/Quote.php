<?php

namespace App\Model\Entities;


use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\BaseEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="quotations")
 * @ORM\EntityListeners({"App\Model\Events\QuoteListener"})
 */
class Quote extends BaseEntity
{
    const STATUS_APPROVED = 1;
    const STATUS_NEED_APPROVAL = 2;
    const STATUS_DENIED = 3;

    public static $ALLOWED_STATUS = [self::STATUS_APPROVED, self::STATUS_DENIED, self::STATUS_NEED_APPROVAL];

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
     * Date when the quotation has been spoken.
     *
     * @ORM\Column(type="datetime")
     * @var DateTime
     */
    private $date;

    /**
     * Date posted.
     *
     * @ORM\Column(type="datetime")
     * @var DateTime
     */
    private $posted;

    /**
     * Date approved.
     *
     * @ORM\Column(type="datetime", nullable=true)
     * @var DateTime
     */
    private $approved;

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

    /**
     * @ORM\Column(type="integer")
     * @var int
     */
    private $status = self::STATUS_NEED_APPROVAL;

    public function __construct()
    {
        $this->posted = new DateTime();
        $this->comments = new ArrayCollection();
        $this->ratings = new ArrayCollection();
        $this->tags = new ArrayCollection();
    }

    /**
     * Returns true if the quote is approved and published.
     *
     * @return bool
     */
    public function isApproved()
    {
        return $this->status == self::STATUS_APPROVED;
    }

    /**
     * @return int
     */
    public function getStaus()
    {
        return $this->status;
    }

    /**
     * @return DateTime
     */
    public function getApproved()
    {
        return $this->approved;
    }

    /**
     * Approves the quote making it visible to the users and increase the poster's rank.
     */
    public function approve()
    {
        $this->approveNoRankUp();
        $this->user->increaseCrank();
    }

    /**
     * Approves the quote.
     */
    public function approveNoRankUp()
    {
        $this->approved = new DateTime();
        $this->setStatus(self::STATUS_APPROVED);
    }

    /**
     * @param $status
     * @throws \InvalidArgumentException
     */
    public function setStatus($status)
    {
        if (!in_array($status, self::$ALLOWED_STATUS)) {
            throw new \InvalidArgumentException("Invalid status code");
        }

        $this->status = $status;
    }

    /**
     * Disapproves the quote and decreases poster's rank.
     */
    public function deny()
    {
        $this->approved = NULL;
        $this->setStatus(self::STATUS_DENIED);
        $this->user->decreaseCrank();
    }

    /**
     * @param Tag $tag
     */
    public function addTag(Tag $tag)
    {
        $this->tags->add($tag);
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
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
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
    public function getPosted()
    {
        return $this->posted;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
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
    public function getUserEmail()
    {
        return $this->user_email;
    }

    /**
     * @param string $user_email
     */
    public function setUserEmail($user_email)
    {
        $this->user_email = $user_email;
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
     * @return User
     */
    public function getUser()
    {
        return $this->user;
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
     * @return Subject
     */
    public function getSubject()
    {
        return $this->subject;
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
     * @return Teacher
     */
    public function getTeacher()
    {
        return $this->teacher;
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
     * @return float
     */
    public function getRating()
    {
        return $this->rating * 1000;
    }

    /**
     * @param float $rating
     */
    public function setRating($rating)
    {
        $this->rating = $rating;
    }

    /**
     * @return ArrayCollection
     */
    public function getTags()
    {
        return $this->tags;
    }

}