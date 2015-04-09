<?php

namespace App\AdminModule\Presenters;

use Kdyby\Doctrine\EntityManager;
use Model;
use Nette;
use Nette\Application\UI\Presenter;

class HomepagePresenter extends Presenter
{
    /** @var EntityManager @inject */
    public $em;

    use BasePresenterTrait;
}
