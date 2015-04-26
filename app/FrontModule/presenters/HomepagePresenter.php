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

    public function renderDefault()
    {
        $template = $this->template;
        $template->quotations = $this->quoteRepository->findAllByDateDesc(self::ITEMS_PER_PAGE, 0);
    }

    public function handleLoad($page)
    {
        if ($page == self::MAX_PAGES_LOAD) {
            $this->sendJson(['more' => true]); // TODO
        }

        $paginator = new Paginator();
        $paginator->setItemsPerPage(self::ITEMS_PER_PAGE);
        $paginator->setPage($page);

        $quotes = $this->quoteRepository->findAllByDateDesc(self::ITEMS_PER_PAGE, $paginator->getOffset());
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
