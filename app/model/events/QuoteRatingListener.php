<?php

namespace App\Model\Events;

use App\Model\Entities\QuoteRating;
use App\Model\Services\RatingService;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreFlushEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;

/**
 * Lifecycle event handler for QuoteRating entity.
 *
 * Class QuoteRatingListener
 * @package App\Model\Events
 */
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
     * @param PreUpdateEventArgs $args
     */
    public function preUpdate(QuoteRating $quoteRating, PreUpdateEventArgs $args)
    {

    }

    /**
     * @param QuoteRating $quoteRating
     * @param PreFlushEventArgs $args
     */
    public function preFlush(QuoteRating $quoteRating, PreFlushEventArgs $args)
    {

    }

    /**
     * @param QuoteRating $quoteRating
     * @param LifecycleEventArgs $args
     */
    public function prePersist(QuoteRating $quoteRating, LifecycleEventArgs $args)
    {

    }

    /**
     * @param QuoteRating $quoteRating
     * @param LifecycleEventArgs $args
     */
    public function preRemove(QuoteRating $quoteRating, LifecycleEventArgs $args)
    {
        $quote = $quoteRating->getQuote();
        $quote->getRatings()->removeElement($quoteRating);

        $this->ratingService->updateQuoteRating($quote);
    }
}