<?php

namespace App\FrontModule\Presenters;

use App\FrontModule\Components\IRateQuoteControlFactory;
use App\Model\Entities\Subject;
use App\Model\Entities\Tag;
use App\Model\Entities\Teacher;
use Nette\Application\BadRequestException;
use Nette\Utils\ArrayHash;
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

    /** @var IRateQuoteControlFactory @inject */
    public $rateQuoteControlFactory;

    /** @var Paginator */
    private $paginator;

    /** @persistent */
    public $quotes = 'latest';

    private $tag = NULL;
    private $teacher = NULL;
    private $subject = NULL;

    /**
     * Prepare paginator.
     */
    protected function startup()
    {
        parent::startup();

        $this->paginator = new Paginator();
        $this->paginator->setItemsPerPage(self::ITEMS_PER_PAGE);
    }

    /**
     * Display search result.
     *
     * @param string $id search query
     */
    public function actionSearch($id)
    {
        $this->setView('default');
        $this->template->initPage = null;
    }

    /**
     * Set OpenGraph data.
     */
    public function beforeRender()
    {
        parent::beforeRender();

        $this->template->section = $this->quotes;
        $og = new ArrayHash();
        $og->title = 'MFFun';
        $og->desc = 'Databáze vtipných citací profesorů z matfyzu.';
        $this->template->og = $og;
    }

    /**
     * Prepares quote data to render.
     */
    public function renderDefault()
    {
        if ($this->action == 'search') {
            $quotes = $this->quoteRepository->search($this->getParameter('id'));
        } else {
            $quotes = $this->getQuotes();
        }


        $template = $this->template;
        $template->quotations = $quotes;
        $template->more = $this->getMore() + 1;
        $template->asTag = function ($name) {
            $tag = explode(' ', $name);
            for ($i = 0; $i < count($tag); $i++) {
                $tag[$i] = ucfirst($tag[$i]);
            }
            $tag = implode('', $tag);
            return '#' . $tag;
        };
    }

    /**
     * Get offset for next 'show more' button.
     *
     * @return float
     */
    private function getMore()
    {
        $displayedPage = ceil($this->paginator->getPage() / self::MAX_PAGES_LOAD);
        return $displayedPage * self::MAX_PAGES_LOAD;
    }

    /**
     * Fetches the quotes by current query.
     *
     * @return array
     */
    private function getQuotes()
    {
        if ($this->tag) {
            $quotes = $this->quoteRepository->findAllByTag($this->tag, self::ITEMS_PER_PAGE, $this->paginator->getOffset());
        } else if ($this->teacher) {
            $quotes = $this->quoteRepository->findAllByTeacher($this->teacher, self::ITEMS_PER_PAGE, $this->paginator->getOffset());
        } else if ($this->subject) {
            $quotes = $this->quoteRepository->findAllBySubject($this->subject, self::ITEMS_PER_PAGE, $this->paginator->getOffset());
        } else {
            $quotes = $this->quoteRepository->findAllApproved(self::ITEMS_PER_PAGE, $this->paginator->getOffset(), $this->quotes);
        }

        if ($this->isAjax() && empty($quotes)) {
            $this->sendJson(['nomore' => true]);
        }

        return $quotes;
    }

    /**
     * Quotes by subject.
     *
     * @param int $id subject ID
     * @param int $p post offset
     * @throws BadRequestException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     */
    public function actionSubject($id, $p)
    {
        /** @var Subject $subj */
        $subj = $this->em->find(Subject::class, $id);
        if (!$subj) throw new BadRequestException;

        $this->subject = $id;
        $this->setView('default');
        $this->resolvePage($p);
        $this->template->subject = $subj->getName();
        $this->template->subjectId = $subj->getId();
    }

    /**
     * Quotes by teacher.
     *
     * @param int $id teacher ID
     * @param int $p post offset
     * @throws BadRequestException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     */
    public function actionTeacher($id, $p)
    {
        /** @var Teacher $teacher */
        $teacher = $this->em->find(Teacher::class, $id);
        if (!$teacher) throw new BadRequestException;

        $this->teacher = $id;
        $this->setView('default');
        $this->resolvePage($p);
        $this->template->teacher = $teacher->getName();
        $this->template->teacherId = $teacher->getId();
    }

    /**
     * Quotes by tag.
     *
     * @param int $id tag ID
     * @param int $p post offset
     * @throws BadRequestException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     */
    public function actionTag($id, $p)
    {
        /** @var Tag $tag */
        $tag = $this->em->find(Tag::class, $id);
        if (!$tag) throw new BadRequestException;

        $this->tag = $id;
        $this->setView('default');
        $this->resolvePage($p);
        $this->template->tag = $tag->getTag();
        $this->template->tagId = $tag->getId();
    }

    /**
     * Get page by quotes offset.
     *
     * @param int $page quote offset
     */
    private function resolvePage($page)
    {
        if ($page) {
            $page = (int)$page;
            $this->paginator->setPage($page);
            $this->template->initPage = $page + 1;
        } else {
            $this->paginator->setPage(1);
            $this->template->initPage = 2;
        }
    }

    /**
     * Homepage.
     */
    public function actionDefault()
    {
        $this->paginator->setPage(1);
        $this->template->initPage = 2;
    }

    /**
     * Allows direct URLs to given page.
     *
     * @param int $id page number
     */
    public function actionPage($id)
    {
        $id = (int)$id;

        $this->setView('default');
        $this->paginator->setPage($id);
        $template = $this->template;
        $template->initPage = $id + 1;
    }

    /**
     * Handles AJAX load for endless scrolling.
     *
     * @param int $page
     */
    public function handleLoad($page)
    {
        $page = (int)$page;

        if ($page > $this->getMore()) {
            $this->sendJson(['more' => true]);
        }

        $this->paginator->setPage($page);
        $quotes = $this->getQuotes();

        $this->setView('loop');
        $this->template->quotations = $quotes;
    }

    /**
     * RateQuoteControl factory.
     *
     * @return \App\FrontModule\Components\RateQuoteControl
     */
    protected function createComponentRateQuote()
    {
        return $this->rateQuoteControlFactory->create();
    }

}
