<?php

namespace App\ApiModule\Presenters;

use App\Model\Entities\Comment;
use App\Model\Repositories\QuoteRepository;
use App\Model\Services\RatingService;
use Doctrine\ORM\Query;
use Kdyby\Doctrine\EntityManager;
use Nette\Application\UI\Presenter;
use Nette\Utils\Json;

class QuotePresenter extends Presenter
{
    /** @var QuoteRepository @inject */
    public $quoteRepository;

    /** @var RatingService @inject */
    public $ratingService;

    /** @var EntityManager @inject */
    public $em;

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
     * [/api/quote/single/X]
     *
     * @param $id
     */
    function actionSingle($id)
    {
        $q = $this->quoteRepository->find($id);

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
     * [/api/quote/mostcommented/?limit=X&offset=Y]
     *
     * @param int $limit
     * @param int $offset
     */
    function actionMostcommented($limit = 10, $offset = 0)
    {
        $q = $this->quoteRepository->findAllApproved($limit, $offset, QuoteRepository::ORDER_COMMENTS);

        $this->sendJson($q);
    }

    /**
     * [/api/quote/random/?limit=X]
     *
     * @param int $limit
     */
    function actionRandom($limit = 10)
    {
        $q = $this->quoteRepository->getRandomQuotes($limit);

        $this->sendJson($q);
    }

    /**
     * [/api/quote/tag/?limit=X&offset=Y]
     *
     * @param int $limit
     * @param int $offset
     */
    function actionTag($id, $limit = 10, $offset = 0)
    {
        $q = $this->quoteRepository->findAllByTag($id, $limit, $offset);

        $this->sendJson($q);
    }

    /**
     * [/api/quote/teacher/?limit=X&offset=Y]
     *
     * @param $id
     * @param int $limit
     * @param int $offset
     */
    function actionTeacher($id, $limit = 10, $offset = 0)
    {
        $q = $this->quoteRepository->findAllByTeacher($id, $limit, $offset);

        $this->sendJson($q);
    }

    /**
     * [/api/quote/subject/?limit=X&offset=Y]
     *
     * @param $id
     * @param int $limit
     * @param int $offset
     */
    function actionSubject($id, $limit = 10, $offset = 0)
    {
        $q = $this->quoteRepository->findAllBySubject($id, $limit, $offset);

        $this->sendJson($q);
    }

    /**
     * [/api/quote/rateUp/X]
     *
     * @param $id
     */
    function actionRateUp($id)
    {
        if (!$this->user->isLoggedIn()) {
            $this->sendJson([
                'success' => false
            ]);
        } else {
            $response = $this->ratingService->rateQuoteUp($id, $this->user->id);

            $this->sendResponse($response);
        }
    }

    /**
     * [/api/quote/rateDown/X]
     *
     * @param $id
     */
    function actionRateDown($id)
    {
        if (!$this->user->isLoggedIn()) {
            $this->sendJson([
                'success' => false
            ]);
        } else {
            $response = $this->ratingService->rateQuoteDown($id, $this->user->id);

            $this->sendResponse($response);
        }
    }
}