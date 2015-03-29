<?php
/**
 * Created by PhpStorm.
 * User: vitush
 * Date: 3/29/15
 * Time: 1:41 PM
 */

namespace App\Model\Events;


use App\Model\Entities\Quote;
use App\Model\Entities\User;
use App\Model\Repositories\QuoteRepository;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Mapping as ORM;

class QuoteListener {

    /** @var QuoteRepository */
    private $quoteRepository;

    public function __construct(QuoteRepository $quoteRepository)
    {
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * Does the automatic approval.
     * Check if quote poster has enough crank to post this quote automatically.
     * If the quote is approved automatically, user's crank will be increased.
     *
     * @param Quote $quote
     * @param LifecycleEventArgs $args
     *
     * @ORM\PrePersist
     */
    public function preFlush(Quote $quote, LifecycleEventArgs $args)
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

}