<?php

namespace App\Model\Services;


use App\Model\Entities\Comment;
use App\Model\Entities\CommentRating;
use App\Model\Entities\Quote;
use App\Model\Entities\QuoteRating;
use App\Model\Entities\User;
use Kdyby\Doctrine\EntityManager;
use Nette\Object;

/**
 * Provides basic Quote and Comment rating functionality.
 *
 * Class RatingService
 * @package App\Model\Services
 */
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

    /**
     * Rates the comment.
     * Adds a new CommentRating instance to the Comment entity's collection.
     *
     * @param Comment $comment
     * @param User $user
     * @param bool $positive is the rating positive?
     */
    public function rateComment(Comment $comment, User $user, $positive)
    {
        /** @var CommentRating $rating */
        $rating = $this->em->getRepository(CommentRating::class)->findOneBy(['user' => $user, 'comment' => $comment]);
        if ($rating) {
            if ($rating->isPositive() && $positive || $rating->isNegative() && !$positive) { // user is removing his rating
                $this->em->remove($rating);
            } else { // user has changed his mind
                $this->em->remove($rating);
                $this->em->flush();

                $new_rating = new CommentRating($user, $comment);
                $positive ? $new_rating->setPositive() : $new_rating->setNegative();

                $this->em->persist($new_rating);
            }
        } else {
            $rating = new CommentRating($user, $comment);
            $positive ? $rating->setPositive() : $rating->setNegative();

            $this->em->persist($rating);
        }
    }

    /**
     * Rates the quote.
     * Adds a new QuoteRating instance to the Quote entity's collection.
     *
     * @param Quote $quote
     * @param User $user
     * @param bool $positive is the rating positive?
     */
    public function rateQuote(Quote $quote, User $user, $positive)
    {
        /** @var QuoteRating $rating */
        $rating = $this->em->getRepository(QuoteRating::class)->findOneBy(['user' => $user, 'quote' => $quote]);
        if ($rating) {
            if ($rating->isPositive() && $positive || $rating->isNegative() && !$positive) { // user is removing his rating
                $this->em->remove($rating);
            } else { // user has changed his mind
                $this->em->remove($rating);
                $this->em->flush();

                $new_rating = new QuoteRating($user, $quote);
                $positive ? $new_rating->setPositive() : $new_rating->setNegative();

                $this->em->persist($new_rating);
            }
        } else {
            $rating = new QuoteRating($user, $quote);
            $positive ? $rating->setPositive() : $rating->setNegative();

            $this->em->persist($rating);
        }
    }

}