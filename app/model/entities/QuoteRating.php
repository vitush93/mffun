<?php

namespace App\Model\Entities;


use Kdyby\Doctrine\Entities\BaseEntity;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToOne;
use DateTime;

/**
 * @ORM\Entity
 * @ORM\Table(name="quote_ratings")
 * @ORM\HasLifecycleCallbacks
 */
class QuoteRating extends BaseEntity
{
    const
        POSITIVE = 10,
        NEGATIVE = 0;
    public static $ALLOWED_RATINGS = array(self::POSITIVE, self::NEGATIVE);

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     * @var integer
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Quote", inversedBy="ratings")
     * @var Quote
     */
    private $quote;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="quote_ratings")
     * @var User
     */
    private $user;

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
        $this->rated = new DateTime();
    }

    /**
     * @ORM\PrePersist
     */
    public function updateQuoteRatingPrePersist()
    {
        $sum = 0;
        foreach ($this->quote->getRatings() as $r) {
            $sum += $r->getValue();
        }
        $this->quote->setRating($sum / ($this->quote->getRatings()->count() * 10));
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param Quote $quote
     */
    public function setQuote($quote)
    {
        $quote->addRating($this);
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
     * @return \DateTime
     */
    public function getRated()
    {
        return $this->rated;
    }

    /**
     * @param User $user
     */
    public function setUser($user)
    {
        $user->addQuoteRating($this);
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
        $this->value = QuoteRating::POSITIVE;
    }

    public function setNegative()
    {
        $this->value = QuoteRating::NEGATIVE;
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