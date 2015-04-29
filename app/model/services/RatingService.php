<?php

namespace App\Model\Services;


use App\Model\Entities\Comment;
use App\Model\Entities\CommentRating;
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

    public function rateComment(Comment $comment, User $user, $positive)
    {
        /** @var CommentRating $rating */
        $rating = $this->em->getRepository(CommentRating::class)->findOneBy(['user' => $user, 'comment' => $comment]);
        if ($rating) {
            if ($rating->isPositive() && $positive || $rating->isNegative() && !$positive) { // user is removing his rating
                $this->em->remove($rating);
            } else { // user has changed his mind
                $positive ? $rating->setPositive() : $rating->setNegative();
            }
        } else {
            $rating = new CommentRating($user, $comment);
            $positive ? $rating->setPositive() : $rating->setNegative();

            $this->em->persist($rating);
        }

    }

    public function rateQuote(Quote $quote, User $user, $positive)
    {
        /** @var QuoteRating $rating */
        $rating = $this->em->getRepository(QuoteRating::class)->findOneBy(['user' => $user, 'quote' => $quote]);
        if ($rating) {
            if ($rating->isPositive() && $positive || $rating->isNegative() && !$positive) { // user is removing his rating
                $this->em->remove($rating);
            } else { // user has changed his mind
                $positive ? $rating->setPositive() : $rating->setNegative();
            }
        } else {
            $rating = new QuoteRating($user, $quote);
            $positive ? $rating->setPositive() : $rating->setNegative();

            $this->em->persist($rating);
        }
    }

    /**
     * Comment rating algo.
     *
     * @param Comment $comment
     */
    public function updateCommentRating(Comment $comment)
    {
        $ups = $this->calculateCommentRatings($comment, CommentRating::POSITIVE);
        $downs = $this->calculateCommentRatings($comment, CommentRating::NEGATIVE);
        $comment->setRatingUp($ups);
        $comment->setRatingDown($downs);
    }

    private function calculateCommentRatings(Comment $comment, $value)
    {
        $sum = 0;
        /** @var CommentRating $r */
        foreach ($comment->getRatings() as $r) {
            if ($r->getValue() == $value) {
                $sum++;
            }
        }
        return $sum;
    }

    /**
     * Temporary rating algorithm.
     *
     * @param Quote $quote
     */
    public function updateQuoteRating(Quote $quote)
    {
        if ($quote->getRatings()->count() == 0) {
            $quote->setRating(0);
            return;
        }

        $sum = 0;
        /** @var QuoteRating $r */
        foreach ($quote->getRatings() as $r) {
            $sum += $r->getValue();
        }

        $quote->setRating($sum);
    }
}