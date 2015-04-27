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

    protected function startup()
    {
        parent::startup();

        $this->paginator = new Paginator();
        $this->paginator->setItemsPerPage(self::ITEMS_PER_PAGE);
    }

    public function renderDefault()
    {
        $template = $this->template;
        $template->quotations = $this->quoteRepository->findAllByDateDesc(self::ITEMS_PER_PAGE, $this->paginator->getOffset());
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

        if ($page == self::MAX_PAGES_LOAD) {
            $this->sendJson(['more' => true]); // TODO
        }

        $this->paginator->setPage($page);

        $quotes = $this->quoteRepository->findAllByDateDesc(self::ITEMS_PER_PAGE, $this->paginator->getOffset());
        if (empty($quotes)) {
            $this->sendJson(['end' => true]);
        }

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
