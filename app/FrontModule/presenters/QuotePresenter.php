<?php
/**
 * Created by PhpStorm.
 * User: Vít
 * Date: 1/21/2015
 * Time: 12:19 AM
 */

namespace App\FrontModule\Presenters;

use App\Model\Entities\Comment;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Form;

class QuotePresenter extends BasePresenter
{
    private $quote;

    public function actionDefault($id)
    {
        $q = $this->quoteRepository->find($id);
        if ($q == null) throw new BadRequestException();

        $this->quote = $q;
    }

    public function renderDefault($id)
    {
        $em = $this->em;

        $this->template->q = $this->quote;

        $this->template->comments = $this->quoteRepository->findTopLevelComments($id);
        $this->template->getReplies = function ($cid) use ($em) {
            $dao = $em->getDao(Comment::getClassName());

            return $dao->findBy(['parent' => $cid], ['posted' => 'ASC']);
        };
        $this->template->getReplyTo = function ($cid) use ($em) {
            $dao = $em->getDao(Comment::getClassName());

            /** @var Comment $c */
            $c = $dao->find($cid);
            return $c->getUser();
        };
    }

    /**
     * [CommentsForm]
     * Post replies.
     *
     * @param Form $form
     */
    public function commentsFormSucceeded(Form $form)
    {
        if (!$this->user->isLoggedIn()) return;

        $parent_id = ($form->getHttpData($form::DATA_TEXT, 'reply-id'));
        $text = ($form->getHttpData($form::DATA_TEXT, 'reply-content'));

        $this->quoteRepository->postComment($text,
            $this->getParameter('id'),
            $this->user->getId(),
            $parent_id);

        $this->em->flush();

        if ($this->isAjax()) {
            $this->redrawControl('comments');
            $this->redrawControl('counts');
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
            $this->redrawControl('comments');
            $this->redrawControl('counts');
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