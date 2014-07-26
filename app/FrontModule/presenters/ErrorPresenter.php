<?php

namespace App\FrontModule;

use Nette,
    Model,
    Tracy\Debugger;


/**
 * Error presenter.
 */
class ErrorPresenter extends BasePresenter
{
    /**
     * @param  Exception
     * @return void
     */
    public function renderDefault($exception)
    {
        if ($this->isAjax()) { // AJAX request? Just note this error in payload.
            $this->payload->error = TRUE;
            $this->terminate();

        } elseif ($exception instanceof Nette\Application\BadRequestException) {
            $code = $exception->getCode();
            if (in_array($code, array(403, 404, 405, 410))) {
                $this->setView('default');
            } else {
                $this->setView(in_array($code, array(403, 404, 405, 410, 500)) ? $code : '4xx');
            }

            Debugger::log("HTTP code $code: {$exception->getMessage()} in {$exception->getFile()}:{$exception->getLine()}", 'access');

        } else {
            $this->setView('500'); // load template 500.latte
            Debugger::log($exception, Debugger::ERROR); // and log exception
        }
    }

}
