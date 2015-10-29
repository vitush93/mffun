<?php

namespace App\ApiModule\Presenters;

use App\Model\Entities\User;
use Kdyby\Doctrine\EntityManager;

class HomepagePresenter extends BasePresenter
{
    /** @var EntityManager @inject */
    public $em;

    function actionDefault()
    {
        $this->sendJson(array(
            'success' => true,
            'message' => 'Hello World!'
        ));
    }

    function actionUser()
    {
        if (!$this->user->isLoggedIn()) $this->error('User not authenticated.');

        /** @var User $user */
        $user = $this->em->find(User::class, $this->user->id);

        $this->sendJson($user->getUserData());
    }

}