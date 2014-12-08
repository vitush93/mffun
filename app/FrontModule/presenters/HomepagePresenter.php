<?php

namespace App\FrontModule\Presenters;

use App\Model\Repositories\QuoteRepository;

class HomepagePresenter extends BasePresenter
{
    /** @var  QuoteRepository @inject */
    public $quoteRepository;

    public function renderDefault()
    {
        $this->template->quotations = $this->quoteRepository->findAllByDateDesc();
    }
}
