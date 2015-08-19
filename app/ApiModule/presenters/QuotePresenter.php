<?php

namespace App\ApiModule\Presenters;

use App\Model\Entities\Quote;
use Doctrine\ORM\Query;
use Kdyby\Doctrine\EntityManager;
use Nette\Application\UI\Presenter;

class QuotePresenter extends Presenter
{
    /** @var EntityManager @inject */
    public $em;

    function actionSingle($id)
    {

    }

    function actionTag($id)
    {

    }

    function actionTeacher($id)
    {

    }

    function actionSubject($id)
    {

    }

    function actionDefault($page)
    {
        $quotes = $this->em->getRepository(Quote::getClassName())
            ->createQuery("SELECT PARTIAL q.{id, text, rating}, t, s, tags
                           FROM App\Model\Entities\Quote q
                           LEFT JOIN q.teacher t
                           LEFT JOIN q.subject s
                           LEFT JOIN q.tags tags
                           WHERE q.status = :status
                           ORDER BY q.id
                           ")
            ->setMaxResults(10)
            ->setParameter('status', Quote::STATUS_APPROVED)
            ->getArrayResult();


        $quote_ids = array_column($quotes, 'id');

        // TODO fetch tags
        // TODO fetch comment counts
        // TODO fetch 3 best comments

        $this->sendJson($quotes);
    }

}