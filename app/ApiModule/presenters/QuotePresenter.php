<?php

namespace App\ApiModule\Presenters;

use App\Model\Entities\Comment;
use App\Model\Entities\Subject;
use App\Model\Entities\Tag;
use App\Model\Entities\Teacher;
use App\Model\Repositories\QuoteOrder;
use App\Model\Repositories\QuoteRepository;
use App\Model\Services\RatingService;
use Doctrine\ORM\Query;
use Kdyby\Doctrine\EntityManager;
use Kdyby\GeneratedProxy\__CG__\App\Model\Entities\Quote;
use Kdyby\Redis\InvalidArgumentException;
use Nette\Application\UI\Presenter;
use Nette\Utils\Json;

class QuotePresenter extends BasePresenter
{
    /** @var QuoteRepository @inject */
    public $quoteRepository;

    /** @var RatingService @inject */
    public $ratingService;

    /** @var EntityManager @inject */
    public $em;

    /**
     * Quotes ordered by date of approval in descending order.
     *
     * @param int $limit
     * @param int $offset
     * @param string $order
     */
    function actionDefault($limit = 10, $offset = 0, $order = 'desc')
    {
        $this->actionDate($limit, $offset, $order);
    }

    /**
     * Quotes ordered by date of approval.
     *
     * @param int $limit
     * @param int $offset
     * @param string $order
     */
    function actionDate($limit = 10, $offset = 0, $order = 'desc')
    {
        if ($order == 'desc') {
            $o = QuoteOrder::DATE_DESC;
        } else if ($order == 'asc') {
            $o = QuoteOrder::DATE_ASC;
        } else {
            $this->error("Invalid 'order' parameter.");

            return;
        }

        $q = $this->quoteRepository->findAllApproved($limit, $offset, $o);

        $this->sendJson($q);
    }

    /**
     * Quotes ordered by number of comments.
     *
     * @param int $limit
     * @param int $offset
     * @param string $order
     */
    function actionComments($limit = 10, $offset = 0, $order = 'desc')
    {
        if ($order == 'desc') {
            $o = QuoteOrder::COMMENTS_DESC;
        } else if ($order == 'asc') {
            $o = QuoteOrder::COMMENTS_ASC;
        } else {
            $this->error("Invalid 'order' parameter.");

            return;
        }

        $q = $this->quoteRepository->findAllApproved($limit, $offset, $o);

        $this->sendJson($q);
    }

    /**
     * @param $id
     */
    function actionSingle($id)
    {
        if ($id == NULL) $this->error("Required parameter 'id' is missing.");

        $q = $this->quoteRepository->find($id);
        if (!$q) {
            $this->error('Quote with id ' . $id . ' was not found.');
        }

        $this->sendJson($q);
    }

    /**
     * @param int $limit
     * @param int $offset
     */
    function actionTop($limit = 10, $offset = 0)
    {
        $q = $this->quoteRepository->findAllApproved($limit, $offset, QuoteOrder::RATING_DESC);

        $this->sendJson($q);
    }

    function actionRating($limit = 10, $offset = 0, $order = 'desc')
    {
        if ($order == 'desc') {
            $o = QuoteOrder::RATING_DESC;
        } else if ($order == 'asc') {
            $o = QuoteOrder::RATING_ASC;
        } else {
            $this->error("Invalid 'order' parameter.");

            return;
        }

        $q = $this->quoteRepository->findAllApproved($limit, $offset, $o);

        $this->sendJson($q);
    }

    /**
     * Quotes ordered by number of comments in descending orders.
     *
     * @param int $limit
     * @param int $offset
     */
    function actionMostcommented($limit = 10, $offset = 0)
    {
        $q = $this->quoteRepository->findAllApproved($limit, $offset, QuoteOrder::COMMENTS_DESC);

        $this->sendJson($q);
    }

    /**
     * @param int $limit
     */
    function actionRandom($limit = 10)
    {
        $q = $this->quoteRepository->getRandomQuotes($limit);

        $this->sendJson($q);
    }

    /**
     * @param $id
     * @param int $limit
     * @param int $offset
     */
    function actionTag($id, $limit = 10, $offset = 0)
    {
        if ($id == NULL) $this->error("Required parameter 'id' is missing.");

        if (!is_numeric($id)) {
            $tag = $this->em->getRepository(Tag::class)->findOneBy(['tag' => $id]);
        } else {
            $tag = $this->em->find(Tag::class, $id);
        }

        if (!$tag) {
            $this->error('Tag ' . $id . ' was not found.');
        }

        $id = $tag->getId();

        $q = $this->quoteRepository->findAllByTag($id, $limit, $offset);

        $this->sendJson($q);
    }

    /**
     * @param $id
     * @param int $limit
     * @param int $offset
     */
    function actionTeacher($id, $limit = 10, $offset = 0)
    {
        if ($id !== NULL) {

            /** @var Teacher $teacher */
            $teacher = $this->em->find(Teacher::class, $id);

            if (!$teacher) {
                $this->error('Teacher with id ' . $id . ' was not found.');
            }
        }

        $q = $this->quoteRepository->findAllByTeacher($id, $limit, $offset);

        $this->sendJson($q);
    }

    /**
     * @param $id
     * @param int $limit
     * @param int $offset
     */
    function actionSubject($id, $limit = 10, $offset = 0)
    {
        if ($id !== NULL) {

            /** @var Subject $subject */
            $subject = $this->em->find(Subject::class, $id);

            if (!$subject) {
                $this->error('Subject with id ' . $id . ' was not found.');
            }
        }

        $q = $this->quoteRepository->findAllBySubject($id, $limit, $offset);

        $this->sendJson($q);
    }

    private function checkQuote($id)
    {
        $quote = $this->em->find(Quote::class, $id);

        if (!$quote) {
            $this->error('Quote with id ' . $id . ' was not found.');
        }
    }

    /**
     * @param $id
     */
    function actionRateUp($id)
    {
        if ($id == NULL) $this->error("Required parameter 'id' is missing.");

        $this->checkQuote($id);

        if (!$this->user->isLoggedIn()) {
            $this->error('User is not logged in.');
        } else {
            $response = $this->ratingService->rateQuoteUp($id, $this->user->id);

            $this->sendResponse($response);
        }
    }

    /**
     * @param $id
     */
    function actionRateDown($id)
    {
        if ($id == NULL) $this->error("Required parameter 'id' is missing.");

        $this->checkQuote($id);

        if (!$this->user->isLoggedIn()) {
            $this->error('User is not logged in.');
        } else {
            $response = $this->ratingService->rateQuoteDown($id, $this->user->id);

            $this->sendResponse($response);
        }
    }
}