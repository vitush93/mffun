<?php

namespace App\FrontModule\Presenters;

use App\Model\Entities\Quote;
use App\Model\Repositories\QuoteRepository;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Form;

class QuotePresenter extends BasePresenter
{
    /** @var QuoteRepository @inject */
    public $quoteRepository;

    /** @var Quote */
    private $quote;

    /**
     * @param $id
     * @throws BadRequestException
     */
    public function actionDefault($id)
    {
        $q = $this->quoteRepository->find($id);
        if ($q == null) throw new BadRequestException();

        $this->quote = $q;
    }

    /**
     * @param $id
     */
    public function renderDefault($id)
    {
        $this->template->q = $this->quote;
    }

    /**
     * [CommentsForm]
     * Post reply.
     *
     * @param Form $form
     */
    public function commentsFormSucceeded(Form $form)
    {
        if (!$this->user->isLoggedIn()) return;

        $parent_id = ($form->getHttpData($form::DATA_TEXT, 'reply-id'));
        $text = ($form->getHttpData($form::DATA_TEXT, 'reply-content'));

        $this->quoteRepository->postComment(
            $text,
            $this->getParameter('id'),
            $this->user->getId(),
            $parent_id);

        $this->em->flush();

        if ($this->isAjax()) {
            $this->redrawControl(); // FIXME nette bug - redraw doesn't work
        } else {
            $this->redirect('this');
        }
    }

    /**
     * CommentsForm factory.
     *
     * @return Form
     */
    public function createComponentCommentsForm()
    {
        $form = new Form();

        $form->onSuccess[] = $this->commentsFormSucceeded;
        $form->addProtection();

        return $form;
    }

    /**
     * [PostCommentForm]
     * Attempts to post a comment.
     *
     * @param Form $form
     * @param $values
     */
    public function postCommentFormSucceeded(Form $form, $values)
    {
        if (!$this->user->isLoggedIn()) return;

        $this->quoteRepository->postComment($values->text,
            $this->getParameter('id'),
            $this->user->getId());

        $this->em->flush();

        if ($this->isAjax()) {
            $this->redrawControl();
        } else {
            $this->redirect('this');
        }
    }

    /**
     * PostCommentForm factory.
     *
     * @return Form
     */
    public function createComponentPostCommentForm()
    {
        $form = new Form();

        $form->addTextArea('text', '')
            ->setAttribute('placeholder', 'napiš něco..')
            ->setRequired('');
        $form->addSubmit('process', '');

        $form->onSuccess[] = $this->postCommentFormSucceeded;
        $form->addProtection();

        return $form;
    }
}