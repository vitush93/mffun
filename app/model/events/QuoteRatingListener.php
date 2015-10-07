<?php

namespace App\Model\Events;

use App\Libs\DifferenceRatingAlgorithm;
use App\Model\Entities\Quote;
use App\Model\Entities\QuoteRating;
use Doctrine\ORM\Event\LifecycleEventArgs;

/**
 * Lifecycle event handler for QuoteRating entity.
 *
 * Class QuoteRatingListener
 * @package App\Model\Events
 */
class QuoteRatingListener
{
    /**
     * @param QuoteRating $quoteRating
     * @param LifecycleEventArgs $args
     */
    public function prePersist(QuoteRating $quoteRating, LifecycleEventArgs $args)
    {
        $quote = $quoteRating->getQuote();
        $this->updateQuoteRating($quote);
    }

    /**
     * @param QuoteRating $quoteRating
     * @param LifecycleEventArgs $args
     */
    public function preRemove(QuoteRating $quoteRating, LifecycleEventArgs $args)
    {
        $quote = $quoteRating->getQuote();
        $quote->getRatings()->removeElement($quoteRating);

        $this->updateQuoteRating($quote);
    }

    /**
     * Quote rating algorithm.
     *
     * @param Quote $quote
     */
    private function updateQuoteRating(Quote $quote)
    {
        $differenceRating = new DifferenceRatingAlgorithm($quote);

        $quote->setRating(
            $differenceRating->calculate()->getResult()
        );
    }
}