<?php

namespace App\FrontModule\Presenters;

use App\Model\Repositories\QuoteRepository;

class HomepagePresenter extends BasePresenter
{
    /** @var  QuoteRepository @inject */
    public $quoteRepository;

    public function actionDefault()
    {
        /*
        $em = $this->em;
        $dao = $em->getDao(Comment::getClassName());
        $comment = new Comment();
        $comment->setQuote($this->quoteRepository->find(8));
        $comment->setText("some comment text");
        $comment->setUser($em->find(User::getClassName(), 3));

        $em->persist($comment);
        $em->flush();
        */
    }

    public function renderDefault()
    {
        $this->template->quotations = $this->quoteRepository->findAllByDateDesc();
    }
}
