<?php

namespace App\ApiModule\Presenters;

use App\Model\Repositories\CommentRepository;
use Doctrine\ORM\EntityNotFoundException;
use Kdyby\Doctrine\EntityManager;
use Nette\InvalidArgumentException;

class CommentPresenter extends BasePresenter
{
    /** @var CommentRepository @inject */
    public $commentRepository;

    /** @var EntityManager @inject */
    public $em;

    /**
     * Fetch first 10 comments its children (3 per comment max).
     *
     * @param int $id Quote id.
     * @param int $limit
     * @param int $offset
     */
    function actionQuote($id, $limit = 10, $offset = 0)
    {
        $comments = $this->commentRepository->quoteComments($id, $limit, $offset, 3);

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