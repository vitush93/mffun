<?php
/**
 * Created by PhpStorm.
 * User: Vít
 * Date: 4/27/2015
 * Time: 7:40 PM
 */

namespace App\Model\Events;


use App\Model\Entities\CommentRating;
use App\Model\Services\RatingService;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreFlushEventArgs;

class CommentRatingListener
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

    public function preFlush(CommentRating $rating, PreFlushEventArgs $args)
    {
        $comment = $rating->getComment();
        $this->ratingService->updateCommentRating($comment);
    }

    public function preRemove(CommentRating $rating, LifecycleEventArgs $args)
    {
        $comment = $rating->getComment();
        $comment->getRatings()->removeElement($rating);

        $this->ratingService->updateCommentRating($comment);
    }

}