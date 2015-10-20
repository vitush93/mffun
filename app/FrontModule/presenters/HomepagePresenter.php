<?php

namespace App\FrontModule\Presenters;

use App\Model\Entities\Subject;
use App\Model\Entities\Tag;
use App\Model\Entities\Teacher;
use App\Model\Repositories\QuoteRepository;
use Doctrine\ORM\Query;
use Kdyby\Doctrine\EntityManager;
use Nette\Application\BadRequestException;
use Nette\Utils\ArrayHash;
use Nette\Utils\Json;
use Nette\Utils\Paginator;

/**
 * Handles displaying a main content loop.
 *
 * Class HomepagePresenter
 * @package App\FrontModule\Presenters
 */
class HomepagePresenter extends BasePresenter
{
    /** Items per loaded page */
    const ITEMS_PER_PAGE = 10;

    /** Maximum of pages to load before displaying 'show more' button */
    const MAX_PAGES_LOAD = 10;

    /** @var Paginator */
    private $paginator;

    /** @var EntityManager @inject */
    public $em;

    /** @var  QuoteRepository @inject */
    public $quoteRepository;

    /**
     * Prepare paginator.
     */
    protected function startup()
    {
        parent::startup();

        $this->paginator = new Paginator();
        $this->paginator->setItemsPerPage(self::ITEMS_PER_PAGE);

        $page = $this->getParameter('p');
        if ($page) {
            $this->paginator->setPage($page);
        }
    }

    /**
     * Display search result.
     *
     * @param string $id search query
     */
    public function actionSearch($id)
    {
        $this->setView('default');

        // TODO
    }

    /**
     * Set OpenGraph data.
     */
    public function beforeRender()
    {
        parent::beforeRender();

        $og = new ArrayHash();
        $og->title = 'MFFun';
        $og->desc = 'Databáze vtipných citací profesorů z matfyzu.';

        $this->template->og = $og;
    }

    /**
     * Quotes by subject.
     *
     * @param int $id subject ID
     * @param int $p post offset
     * @throws BadRequestException
     */
    public function actionSubject($id, $p)
    {
        /** @var Subject $subj */
        $subj = $this->em->find(Subject::class, $id);
        if (!$subj) throw new BadRequestException;

        $this->setView('default');

        // TODO
    }

    /**
     * Quotes by teacher.
     *
     * @param int $id teacher ID
     * @param int $p post offset
     * @throws BadRequestException
     */
    public function actionTeacher($id, $p)
    {
        /** @var Teacher $teacher */
        $teacher = $this->em->find(Teacher::class, $id);
        if (!$teacher) throw new BadRequestException;

        $this->setView('default');

        // TODO
    }

    /**
     * Quotes by tag.
     *
     * @param int $id tag ID
     * @param int $p post offset
     * @throws BadRequestException
     */
    public function actionTag($id, $p)
    {
        /** @var Tag $tag */
        $tag = $this->em->find(Tag::class, $id);
        if (!$tag) throw new BadRequestException;

        $this->setView('default');

        // TODO
    }

    /**
     * Homepage.
     *
     * @param $p
     * @throws \Nette\Utils\JsonException
     */
    public function actionDefault($p)
    {
        $quotes = $this->getQuotes(QuoteRepository::ORDER_LATEST);

        $this->template->quotes = Json::encode($quotes);
    }

    /**
     * Homepage
     *
     * @param $p
     * @throws \Nette\Utils\JsonException
     */
    function actionTop($p)
    {
        $quotes = $this->getQuotes(QuoteRepository::ORDER_TOP);

        $this->setView('default');
        $this->template->quotes = Json::encode($quotes);
    }

    /**
     * Homepage
     *
     * @param $p
     * @throws \Nette\Utils\JsonException
     */
    function actionMostcommented($p)
    {
        $quotes = $this->getQuotes(QuoteRepository::ORDER_COMMENTS);

        $this->setView('default');
        $this->template->quotes = Json::encode($quotes);
    }

    /**
     * Homepage
     *
     * @param $p
     * @throws \Nette\Utils\JsonException
     */
    function actionRandom($p)
    {
        $quotes = $this->quoteRepository->getRandomQuotes(10);

        $this->setView('default');
        $this->template->quotes = Json::encode($quotes);
    }

    /**
     * @param $order
     * @return array
     */
    private function getQuotes($order)
    {
        $quotes = $this->quoteRepository->findAllApproved(self::ITEMS_PER_PAGE, $this->paginator->getOffset(), $order);

        return $quotes;
    }
}