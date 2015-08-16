<?php

namespace App\ApiModule\Presenters;

use Nette\Application\UI\Presenter;

class HomepagePresenter extends Presenter
{
    function actionDefault()
    {
        $this->sendJson(array(
            'success' => true,
            'message' => 'Hello World!'
        ));
    }

}