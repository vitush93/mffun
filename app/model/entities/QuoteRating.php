<?php

namespace App\Model\Entities;


use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\BaseEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="quote_ratings", uniqueConstraints={@ORM\UniqueConstraint(name="rating_unique", columns={"quote_id", "user_id"})})
 * @ORM\EntityListeners({"App\Model\Events\QuoteRatingListener"})
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
     * @ORM\ManyToOne(targetEntity="Quote", inversedBy="ratings", cascade={"persist"})
     * @var Quote
     */
    private $quote;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="quote_ratings", cascade={"persist"})
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

    /**
     * @param User $user
     * @param Quote $quote
     */
    public function __construct(User $user, Quote $quote)
    {
        $this->rated = new DateTime();
        $this->setUser($user);
        $this->setQuote($quote);
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

    public function isPositive()
    {
        return $this->value == QuoteRating::POSITIVE;
    }

    public function isNegative()
    {
        return $this->value == QuoteRating::NEGATIVE;
    }
} 