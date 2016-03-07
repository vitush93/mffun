<?php

namespace App\ApiModule\Presenters;

use App\Model\Entities\Comment;
use App\Model\Entities\Quote;
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
     * Fetch all quote comments by quote id.
     *
     * @param int $id Quote id.
     */
    function actionQuote($id)
    {
        if ($id == NULL) $this->error("Required parameter 'id' not specified.");

        $quote = $this->em->find(Quote::class, $id);
        if (!$quote) {
            $this->error('Quote with id ' . $id . ' was not found.');
        }

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
        if ($id == NULL) $this->error("Required parameter 'id' not specified.");

        /** @var Comment $comment */
        $comment = $this->em->find(Comment::class, $id);
        if (!$comment) {
            $this->error('Comment with id ' . $id . ' was not found.');
        } else if ($comment->getParent() > 0) {
            $this->error('Comment with id ' . $id . ' is not a root comment.');
        }

        $comments = $this->commentRepository->thread($id, $limit, $offset);

        $this->sendJson($comments);
    }

    /**
     * @param int $id Comment id.
     * @param string $rate either 'up' or 'down'
     */
    function actionRate($id, $rate)
    {
        if (!$this->user->isLoggedIn()) $this->error('Not authenticated');

        if ($id == NULL) $this->error("Required parameter 'id' not specified.");

        if ($rate != 'up' && $rate != 'down') $this->error('Rate parameter can be either "up" or "down"');

        /** @var Comment $comment */
        $comment = $this->em->find(Comment::class, $id);
        if (!$comment) $this->error('Comment with id ' . $id . ' not found.');

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