<?php

namespace App\FrontModule\Presenters;

use App\Model\Repositories\QuoteRepository;
use Nette\Application\UI\Presenter;

class HomepagePresenter extends Presenter
{

    /** @var QuoteRepository @inject */
    public $quoteRepository;

    use BasePresenterTrait;

    public function renderDefault()
    {
        $this->template->quotations = $this->quoteRepository->findAllByDateDesc(5);
        $this->template->tags = $this->quoteRepository->getTagCloud();
    }

}
