<?php

namespace App\Model\Services;


use App\Model\Entities\Quote;
use App\Model\Entities\QuoteRating;
use App\Model\Entities\User;
use Kdyby\Doctrine\EntityManager;
use Nette\Object;

class RatingService extends Object
{
    /** @var EntityManager */
    private $em;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

    public function rate(Quote $quote, User $user, $positive)
    {
        /** @var QuoteRating $rating */
        $rating = $this->em->getRepository(QuoteRating::class)->findOneBy(['user' => $user, 'quote' => $quote]);
        if ($rating) {
            $positive ? $rating->setPositive() : $rating->setNegative();
        } else {
            $rating = new QuoteRating($user, $quote);
            $positive ? $rating->setPositive() : $rating->setNegative();

            $this->em->persist($rating);
        }
    }

    /**
     * Temporary rating algorithm.
     *
     * @param Quote $quote
     */
    public function updateQuoteRating(Quote $quote)
    {
        $sum = 0;
        foreach ($quote->getRatings() as $r) {
            $sum += $r->getValue();
        }
        $ratio = $sum / ($quote->getRatings()->count() * 10);
        $quote->setRating($ratio * 100);
    }
}