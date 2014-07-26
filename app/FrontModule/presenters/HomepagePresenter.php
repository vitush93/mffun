<?php

namespace App\FrontModule;
use App\Model\Articles;


/**
 * Homepage presenter.
 */
class HomepagePresenter extends BasePresenter
{
    private $articles;
    
    public function actionDefault()
    {
        $this->template->articles = $this->articles->findAll();
    }
    
    public function injectArticles(Articles $articles)
    {
        $this->articles = $articles;
    }

}
