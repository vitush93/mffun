<?php

namespace App\ApiModule\Presenters;

use App\Model\Entities\Comment;
use App\Model\Entities\Subject;
use App\Model\Entities\Tag;
use App\Model\Entities\Teacher;
use App\Model\Repositories\QuoteRepository;
use App\Model\Services\RatingService;
use Doctrine\ORM\Query;
use Kdyby\Doctrine\EntityManager;
use Kdyby\GeneratedProxy\__CG__\App\Model\Entities\Quote;
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
        if (!$q) {
            $this->error('Quote with id ' . $id . ' was not found.');
        }

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
     * @param $id
     * @param int $limit
     * @param int $offset
     */
    function actionTag($id, $limit = 10, $offset = 0)
    {
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
     * [/api/quote/teacher/?limit=X&offset=Y]
     *
     * @param $id
     * @param int $limit
     * @param int $offset
     */
    function actionTeacher($id, $limit = 10, $offset = 0)
    {
        /** @var Teacher $teacher */
        $teacher = $this->em->find(Teacher::class, $id);

        if (!$teacher) {
            $this->error('Teacher with id ' . $id . ' was not found.');
        }

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
        /** @var Subject $subject */
        $subject = $this->em->find(Subject::class, $id);

        if (!$subject) {
            $this->error('Subject with id ' . $id . ' was not found.');
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
     * [/api/quote/rateUp/X]
     *
     * @param $id
     */
    function actionRateUp($id)
    {
        $this->checkQuote($id);

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
        $this->checkQuote($id);

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