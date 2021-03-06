<?php

namespace App\Model\Events;


use App\Model\Entities\Quote;
use App\Model\Entities\User;
use App\Model\Repositories\QuoteRepository;
use App\Model\Services\RatingService;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreFlushEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Mapping as ORM;

/**
 * Lifecycle event handler for Quote entity.
 *
 * Class QuoteListener
 * @package App\Model\Events
 */
class QuoteListener
{

    /** @var QuoteRepository */
    private $quoteRepository;

    /** @var RatingService */
    private $ratingService;

    /**
     * @param QuoteRepository $quoteRepository
     * @param RatingService $ratingService
     */
    public function __construct(QuoteRepository $quoteRepository, RatingService $ratingService)
    {
        $this->quoteRepository = $quoteRepository;
        $this->ratingService = $ratingService;
    }

    /**
     * Does the automatic approval.
     * Check if quote poster has enough crank to post this quote automatically.
     *
     * @param Quote $quote
     */
    private function autoApprove(Quote $quote)
    {
        if (!$quote->getUser()) return; // anonymous user (null value)

        if ($quote->getUser()->getRole() == User::ROLE_ADMIN || $quote->getUser()->getRole() == User::ROLE_MODERATOR) {
            $quote->approve();

            return;
        }

        if ($quote->getUser()->getCrank() > 0) {
            $quote->approve();
        }
    }

    /**
     * @param Quote $quote
     * @param PreUpdateEventArgs $args
     */
    public function preUpdate(Quote $quote, PreUpdateEventArgs $args)
    {

    }

    /**
     * @param Quote $quote
     * @param PreFlushEventArgs $args
     */
    public function preFlush(Quote $quote, PreFlushEventArgs $args)
    {

    }

    /**
     * @param Quote $quote
     * @param LifecycleEventArgs|PreFlushEventArgs $args
     */
    public function prePersist(Quote $quote, LifecycleEventArgs $args)
    {
        $this->autoApprove($quote);
    }

}