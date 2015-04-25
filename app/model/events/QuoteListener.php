<?php

namespace App\Model\Events;


use App\Model\Entities\Quote;
use App\Model\Entities\User;
use App\Model\Repositories\QuoteRepository;
use App\Model\Services\RatingService;
use Doctrine\ORM\Event\PreFlushEventArgs;
use Doctrine\ORM\Mapping as ORM;

class QuoteListener {

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
     * If the quote is approved automatically, user's crank will be increased.
     *
     * @param Quote $quote
     */
    private function autoApprove(Quote $quote)
    {
        if ($quote->getUser()->getUsername() === 'unknown') return;

        if ($quote->getUser()->getRole() == User::ROLE_ADMIN || $quote->getUser()->getRole() == User::ROLE_MODERATOR) {
            $quote->approveNoRankUp();

            return;
        }

        if ($quote->getUser()->getCrank() > 0) {
            $quote->approveNoRankUp();
        }
    }

    /**
     * @param Quote $quote
     * @param PreFlushEventArgs $args
     */
    public function preFlush(Quote $quote, PreFlushEventArgs $args)
    {
        $this->autoApprove($quote);
    }

}