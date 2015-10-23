<?php

namespace App\ApiModule\Presenters;


use Nette\Application\UI\Presenter;
use Nette\Http;

abstract class BasePresenter extends Presenter
{
    public function error($message = NULL, $code = Http\IResponse::S404_NOT_FOUND)
    {
        $this->sendJson([
            'error' => $code,
            'message' => $message
        ]);
    }

}