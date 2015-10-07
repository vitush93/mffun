<?php

namespace App\Model\Events;


use App\Libs\BooleanRatingAlgorithm;
use App\Model\Entities\Comment;
use App\Model\Entities\CommentRating;
use Doctrine\ORM\Event\LifecycleEventArgs;

/**
 * Lifecycle event handler for CommentRating entity.
 *
 * Class CommentRatingListener
 * @package App\Model\Events
 */
class CommentRatingListener
{
    /**
     * @param CommentRating $commentRating
     * @param LifecycleEventArgs $args
     */
    public function prePersist(CommentRating $commentRating, LifecycleEventArgs $args)
    {
        $comment = $commentRating->getComment();
        $this->updateCommentRating($comment);
    }

    /**
     * @param CommentRating $rating
     * @param LifecycleEventArgs $args
     */
    public function preRemove(CommentRating $rating, LifecycleEventArgs $args)
    {
        $comment = $rating->getComment();
        $comment->getRatings()->removeElement($rating);

        $this->updateCommentRating($comment);
    }

    /**
     * Regenerates calculates rating value for given Comment entity.
     *
     * @param Comment $comment
     */
    private function updateCommentRating(Comment $comment)
    {
        $booleanRating = new BooleanRatingAlgorithm($comment);

        $comment->setRatingUp(
            $booleanRating->calculateBy(CommentRating::POSITIVE)->getResult()
        );
        $comment->setRatingDown(
            $booleanRating->calculateBy(CommentRating::NEGATIVE)->getResult()
        );
    }

}