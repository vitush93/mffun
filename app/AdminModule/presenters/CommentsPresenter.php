<?php

namespace App\AdminModule\Presenters;


use App\Model\Entities\Comment;
use Nette\Application\BadRequestException;

class CommentsPresenter extends BasePresenter
{
    public function renderDefault()
    {
        $this->template->comments = $this->em->getRepository(Comment::class)->findAll();
    }

    public function handleDelete($id)
    {
        /** @var Comment $comment */
        $comment = $this->em->find(Comment::class, $id);
        if (!$comment) throw new BadRequestException;

        if ($comment->getParent() == 0) {
            $children = $this->em->getRepository(Comment::class)->findBy(['parent' => $id]);
            foreach ($children as $c) {
                $this->em->remove($c);
            }
        }
        $this->em->remove($comment);

        $this->em->flush();

        if (!$this->isAjax()) {
            $this->flashMessage('Komentář byl odstraněn.', 'info');
            $this->redirect('this');
        }
    }
}