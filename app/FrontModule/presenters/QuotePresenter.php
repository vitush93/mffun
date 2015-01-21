<?php
/**
 * Created by PhpStorm.
 * User: Vít
 * Date: 1/21/2015
 * Time: 12:19 AM
 */

namespace App\FrontModule\Presenters;

use App\Model\Entities\Comment;
use App\Model\Repositories\QuoteRepository;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Form;

class QuotePresenter extends BasePresenter
{
    /** @var QuoteRepository @inject */
    public $quoteRepository;
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

        return $form;
    }
}