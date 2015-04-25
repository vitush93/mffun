<?php

namespace App\FrontModule\Components;

interface IRateCommentControlFactory
{
    /** @return RateCommentControl */
    function create();
}

use Kdyby\Doctrine\EntityManager;
use Nette\Application\UI\Control;

class RateCommentControl extends Control
{
    /** @var EntityManager */
    private $em;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

    public function render($c)
    {
        $template = $this->template;

        $template->up = $c['up'];
        $template->down = $c['down'];
        $template->cid = $c['cid'];

        $template->setFile(__DIR__ . '/RateComment.latte');
        $template->render();
    }

    public function handleUp($cid)
    {
        // TODO
        $this->presenter->sendJson([
            'cid' => $cid,
            'rate' => 'up'
        ]);
    }

    public function handleDown($cid)
    {
        // TODO
        $this->presenter->sendJson([
            'cid' => $cid,
            'rate' => 'down'
        ]);
    }


}