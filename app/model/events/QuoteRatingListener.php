<?php

namespace App\Model\Events;

use App\Model\Entities\QuoteRating;
use App\Model\Services\RatingService;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreFlushEventArgs;

class QuoteRatingListener
{
    /** @var RatingService */
    private $ratingService;

    /**
     * @param RatingService $ratingService
     */
    public function __construct(RatingService $ratingService)
    {
        $this->ratingService = $ratingService;
    }

    /**
     * @param QuoteRating $quoteRating
     * @param PreFlushEventArgs $args
     */
    public function preFlush(QuoteRating $quoteRating, PreFlushEventArgs $args)
    {
        $this->ratingService->updateQuoteRating($quoteRating->getQuote());
    }

    public function preRemove(QuoteRating $quoteRating, LifecycleEventArgs $args)
    {
        $quote = $quoteRating->getQuote();
        $quote->getRatings()->removeElement($quoteRating);

        $this->ratingService->updateQuoteRating($quote);
    }
}