<?php

namespace App\ApiModule\Presenters;

use App\Model\Repositories\CommentRepository;

class CommentPresenter extends BasePresenter
{
    /** @var CommentRepository @inject */
    public $commentRepository;

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

    function actionPost($id = NULL, $quote = NULL)
    {
        if ($id == NULL && $quote == NULL) {
            $this->error('No comment id nor quote id provided.');
        }

        if (!$this->request->isMethod('POST')) {
            $this->error('Incorrect method: '.$this->request->method.'. Use POST instead.');
        }

        dump($this->request->getPost()); // TODO
    }
}