<?php

namespace App\ApiModule\Presenters;

use App\Model\Entities\Comment;
use App\Model\Entities\User;
use App\Model\Repositories\CommentRepository;
use App\Model\Services\RatingService;
use Doctrine\ORM\EntityNotFoundException;
use Kdyby\Doctrine\EntityManager;
use Nette\InvalidArgumentException;

class CommentPresenter extends BasePresenter
{
    /** @var CommentRepository @inject */
    public $commentRepository;

    /** @var RatingService @inject */
    public $ratingService;

    /** @var EntityManager @inject */
    public $em;

    /**
     * Fetch first 10 comments its children (3 per comment max).
     *
     * @param int $id Quote id.
     */
    function actionQuote($id)
    {
        $comments = $this->commentRepository->quoteComments($id);

        $this->sendJson($comments);
    }

    /**
     * @param int $id Comment id.
     * @param $limit
     * @param $offset
     */
    function actionThread($id, $limit = 3, $offset = 0)
    {
        $comments = $this->commentRepository->thread($id, $limit, $offset);

        $this->sendJson($comments);
    }

    function actionRate($id, $rate)
    {
        if (!$this->user->isLoggedIn()) $this->error('Not authenticated');

        if ($rate != 'up' && $rate != 'down') $this->error('Rate parameter can be either "up" or "down"');

        /** @var Comment $comment */
        $comment = $this->em->find(Comment::class, $id);
        if (!$comment) $this->error('Entity not found.');

        /** @var User $user */
        $user = $this->em->find(User::class, $this->user->id);

        $this->ratingService->rateComment($comment, $user, ($rate == 'up'));
        $this->em->flush();

        $this->sendJson([
            'success' => true,
            'up' => $comment->getRatingUp(),
            'down' => $comment->getRatingDown()
        ]);
    }

    /**
     * @param int $id parent Comment id.
     * @param null $quote
     */
    function actionPost($id = 0, $quote)
    {
        if (!$this->user->isLoggedIn()) $this->error('Not authenticated.');

        if ($quote == NULL) $this->error('No quote id provided.');

        if (!$this->request->isMethod('POST')) $this->error('Incorrect method: ' . $this->request->method . '. Use POST instead.');

        try {
            $new_comment = $this->commentRepository->create($this->request->getPost('text'), $quote, $this->user->id, $id);
            $this->em->flush();

            $this->sendJson([
                'success' => true,
                'comment' => $new_comment
            ]);
        } catch (EntityNotFoundException $e) {
            $this->error($e->getMessage());
        } catch (InvalidArgumentException $e) {
            $this->error($e->getMessage());
        }
    }
}