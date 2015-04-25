<?php

namespace App\FrontModule\Presenters;

use App\FrontModule\Components\IRateQuoteControlFactory;
use App\Model\Repositories\QuoteRepository;

class HomepagePresenter extends BasePresenter
{
    /** @var IRateQuoteControlFactory @inject */
    public $rateQuoteControlFactory;

    /** @var QuoteRepository @inject */
    public $quoteRepository;

    public function beforeRender()
    {
        $template = $this->template;
        $template->quotations = $this->quoteRepository->findAllByDateDesc(10);
        $template->tags = $this->quoteRepository->getTagCloud();
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
