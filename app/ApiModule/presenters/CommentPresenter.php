<?php
/**
 * Created by PhpStorm.
 * User: vitush
 * Date: 22.10.2015
 * Time: 13:01
 */

namespace App\ApiModule\Presenters;


use App\Model\Entities\Comment;
use App\Model\Entities\Quote;
use Kdyby\Doctrine\EntityManager;
use Nette\Application\UI\Presenter;
use Nette\Utils\Json;

class CommentPresenter extends Presenter
{
    /** @var EntityManager @inject */
    public $em;

    function actionQuote($id)
    {
        $q = $this->em->getRepository(Quote::class)->find($id);

        $comments = $this->em->getRepository(Comment::class)->findBy(array(
            'quote' => $q,
            'parent' => 0
        ), array('posted' => 'DESC'));

        $arr[0] = $comments;

        /** @var Comment $c */
        foreach ($comments as $c) {

            /** @var Comment $child */
            foreach ($this->em->getRepository(Comment::class)->findBy(array(
                'quote' => $q,
                'parent' => $c->getId()
            )) as $child) {
                $index = $c->getId();
                $arr[$index][] = $child;
            }
        }

        $this->sendJson($arr);
    }
}