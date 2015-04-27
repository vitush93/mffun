<?php

namespace App\FrontModule\Components;

interface IRateCommentControlFactory
{
    /** @return RateCommentControl */
    function create();
}

use App\Model\Entities\Comment;
use App\Model\Entities\CommentRating;
use App\Model\Entities\User;
use App\Model\Services\RatingService;
use Kdyby\Doctrine\EntityManager;
use Nette\Application\UI\Control;

class RateCommentControl extends Control
{
    /** @var RatingService */
    private $ratingService;

    /** @var EntityManager */
    private $em;

    /**
     * @param EntityManager $entityManager
     * @param RatingService $ratingService
     */
    public function __construct(EntityManager $entityManager, RatingService $ratingService)
    {
        $this->em = $entityManager;
        $this->ratingService = $ratingService;
    }

    public function render($c)
    {
        $template = $this->template;

        $template->up = $c['up'];
        $template->down = $c['down'];
        $template->cid = $c['cid'];

        $this->template->ratedUp = null;
        $this->template->ratedDown = null;

        if ($this->presenter->user->isLoggedIn()) {
            $uid = $this->presenter->user->id;
            $user = $this->em->find(User::class, $uid);
            $comment = $this->em->find(Comment::class, $c['cid']);

            /** @var CommentRating|null $r */
            $r = $this->em->getRepository(CommentRating::class)->findOneBy(['comment' => $comment, 'user' => $user]);
            if ($r) {
                $r->isPositive() ? $this->template->ratedUp = true : $this->template->ratedDown = true;
            }
        }

        $template->setFile(__DIR__ . '/RateComment.latte');
        $template->render();
    }

    public function handleUp($cid)
    {
        if (!$this->presenter->user->isLoggedIn()) $this->presenter->sendJson([]);

        /** @var Comment $comment */
        $comment = $this->em->find(Comment::class, $cid);

        /** @var User $user */
        $user = $this->em->find(User::class, $this->presenter->user->id);

        $this->ratingService->rateComment($comment, $user, true);
        $this->em->flush();

        $this->presenter->sendJson(
            [
                'cid' => $cid,
                'rate' => 'up',
                'ups' => $comment->getRatingUp(),
                'downs' => $comment->getRatingDown()
            ]
        );
    }

    public function handleDown($cid)
    {
        if (!$this->presenter->user->isLoggedIn()) $this->presenter->sendJson([]);

        /** @var Comment $comment */
        $comment = $this->em->find(Comment::class, $cid);

        /** @var User $user */
        $user = $this->em->find(User::class, $this->presenter->user->id);

        $this->ratingService->rateComment($comment, $user, false);
        $this->em->flush();

        $this->presenter->sendJson(
            [
                'cid' => $cid,
                'rate' => 'down',
                'ups' => $comment->getRatingUp(),
                'downs' => $comment->getRatingDown()
            ]
        );
    }


}