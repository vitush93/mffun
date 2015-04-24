<?php

namespace App\FrontModule\Presenters;

use App\Model\Repositories\QuoteRepository;

class HomepagePresenter extends BasePresenter
{

    /** @var QuoteRepository @inject */
    public $quoteRepository;

    public function beforeRender()
    {
        $template = $this->template;
        $template->quotations = $this->quoteRepository->findAllByDateDesc(10);
        $template->tags = $this->quoteRepository->getTagCloud();
    }

}
