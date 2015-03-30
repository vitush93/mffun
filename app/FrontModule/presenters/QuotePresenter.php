<?php
/**
 * Created by PhpStorm.
 * User: Vít
 * Date: 1/21/2015
 * Time: 12:19 AM
 */

namespace App\FrontModule\Presenters;

use App\Model\Entities\Comment;
use App\Model\Entities\Quote;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Form;

class QuotePresenter extends BasePresenter
{
    /** @var Quote */
    private $quote;

    /** @var  ArrayCollection */
    private $comments;

    public function actionDefault($id)
    {
        $q = $this->quoteRepository->find($id);
        if ($q == null) throw new BadRequestException();

        $this->quote = $q;
    }

    public function renderDefault($id)
    {
        $this->template->q = $this->quote;
        $this->template->comments = $this->quoteRepository->getTopLevelComments($this->quote);

        $this->initRepliesLambda();
        $this->initReplyUserLamda();
    }

    /**
     * Initialize closure for template.
     * getReplies closure will find replies for given comment id.
     */
    private function initRepliesLambda()
    {
        // comment responses closure - find responses to given comment
        $this->template->getReplies = function ($cid) {
//            return $this->quote->getComments()->matching( TODO reduce database queries
//                Criteria::create()
//                    ->where(Criteria::expr()->eq('parent', $cid))
//                    ->orderBy(['posted' => 'ASC'])
//            );
            return $this->em->getRepository(Comment::class)->findBy(['parent' => $cid], ['posted' => 'ASC']);
        };
    }

    /**
     * Initialize closure for template.
     * getReplyTo closure will fetch the parent comment's poster username.
     */
    private function initReplyUserLamda()
    {
        // reply to closure
        $this->template->getReplyTo = function ($cid) {

            /** @var Comment $c */
            $c = $this->quote->getComments()->matching(
                Criteria::create()
                    ->where(Criteria::expr()->eq('id', $cid))
            )->first();

            return $c->getUser();
        };
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
            $this->redrawControl();
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