<?php

namespace App\AdminModule\Presenters;

use Model;
use Nette;

class HomepagePresenter extends BasePresenter
{
    public function actionDefault()
    {
        $this->redirect('Quotes:default');
    }
}
