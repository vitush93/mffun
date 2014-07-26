<?php

namespace App\FrontModule;
use App\Model\Articles;


/**
 * Homepage presenter.
 */
class HomepagePresenter extends BasePresenter
{
    private $articles;
    
    public function injectArticles(Articles $articles)
    {
        $this->articles = $articles;
    }

}
