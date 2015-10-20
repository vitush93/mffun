<?php

namespace App\ApiModule\Presenters;

use App\Model\Repositories\QuoteRepository;
use Doctrine\ORM\Query;
use Nette\Application\UI\Presenter;

class QuotePresenter extends Presenter
{
    /** @var QuoteRepository @inject */
    public $quoteRepository;

    /**
     * [/api/quote/?limit=X&offset=Y]
     *
     * @param int $limit
     * @param int $offset
     */
    function actionDefault($limit = 10, $offset = 0)
    {
        $q = $this->quoteRepository->findAllApproved($limit, $offset, QuoteRepository::ORDER_LATEST);

        $this->sendJson($q);
    }

    /**
     * [/api/quote/top/?limit=X&offset=Y]
     *
     * @param int $limit
     * @param int $offset
     */
    function actionTop($limit = 10, $offset = 0)
    {
        $q = $this->quoteRepository->findAllApproved($limit, $offset, QuoteRepository::ORDER_TOP);

        $this->sendJson($q);
    }

    /**
     * [/api/quote/mostcomments/?limit=X&offset=Y]
     *
     * @param int $limit
     * @param int $offset
     */
    function actionMostcommented($limit = 10, $offset = 0)
    {
        $q = $this->quoteRepository->findAllApproved($limit, $offset, QuoteRepository::ORDER_COMMENTS);

        $this->sendJson($q);
    }
}