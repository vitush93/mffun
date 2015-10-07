<?php

namespace App\ApiModule\Presenters;

use App\Model\Services\QuoteRestService;
use Doctrine\ORM\Query;
use Nette\Application\UI\Presenter;

class QuotePresenter extends Presenter
{
    /** @var QuoteRestService @inject */
    public $quoteRestService;

    /**
     * [/api/quote/?limit=X&offset=Y]
     *
     * @param int $limit
     * @param int $offset
     */
    function actionDefault($limit = 10, $offset = 0)
    {
        $this->sendResponse(
            $this->quoteRestService->latest($limit, $offset)
        );
    }

    /**
     * [/api/quote/top/?limit=X&offset=Y]
     *
     * @param int $limit
     * @param int $offset
     */
    function actionTop($limit = 10, $offset = 0)
    {
        $this->sendResponse(
            $this->quoteRestService->top($limit, $offset)
        );
    }

    /**
     * [/api/quote/mostcomments/?limit=X&offset=Y]
     *
     * @param int $limit
     * @param int $offset
     */
    function actionMostcommented($limit = 10, $offset = 0)
    {
        $this->sendResponse(
            $this->quoteRestService->mostCommented($limit, $offset)
        );
    }
}