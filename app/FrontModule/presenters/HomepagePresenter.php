<?php

namespace App\FrontModule\Presenters;

use App\FrontModule\Components\IRateQuoteControlFactory;
use Nette\Utils\Paginator;

class HomepagePresenter extends BasePresenter
{
    const ITEMS_PER_PAGE = 10;
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

    protected function startup()
    {
        parent::startup();

        $this->paginator = new Paginator();
        $this->paginator->setItemsPerPage(self::ITEMS_PER_PAGE);
    }

    public function beforeRender()
    {
        parent::beforeRender();

        $this->template->section = $this->quotes;
    }

    public function renderDefault()
    {
        $quotes = $this->getQuotes();

        $template = $this->template;
        $template->quotations = $quotes;
        $template->more = $this->getMore() + 1;
    }

    private function getMore()
    {
        $displayedPage = ceil($this->paginator->getPage() / self::MAX_PAGES_LOAD);
        return $displayedPage * self::MAX_PAGES_LOAD;
    }

    private function getQuotes()
    {
        if ($this->tag) {
            $quotes = $this->quoteRepository->findAllByTag($this->tag, self::ITEMS_PER_PAGE, $this->paginator->getOffset());
        } else if ($this->teacher) {

        } else if ($this->subject) {

        } else {
            $quotes = $this->quoteRepository->findAllApproved(self::ITEMS_PER_PAGE, $this->paginator->getOffset(), $this->quotes);
        }

        if (empty($quotes)) {
            $this->sendJson(['nomore' => true]);
        }

        return $quotes;
    }

    public function actionTag($id)
    {
        $this->tag = $id;
        $this->setView('default');
        $this->template->initPage = 2;
    }

    public function actionDefault()
    {
        $this->paginator->setPage(1);
        $this->template->initPage = 2;
    }

    public function actionPage($id)
    {
        $id = (int)$id;

        $this->setView('default');
        $this->paginator->setPage($id);
        $template = $this->template;
        $template->initPage = $id + 1;
    }

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
