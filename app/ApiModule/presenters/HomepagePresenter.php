<?php

namespace App\ApiModule\Presenters;

use App\Model\Entities\User;
use Kdyby\Doctrine\EntityManager;

class HomepagePresenter extends BasePresenter
{
    /** @var EntityManager @inject */
    public $em;

    /**
     * Render API documentation.
     */
    function actionDefault()
    {
        $this->template->setFile(__DIR__ . '/../../../docs/api.html'); // generated using swagger and bootprint
    }

    function actionUser()
    {
        if (!$this->user->isLoggedIn()) $this->error('User not authenticated.');

        /** @var User $user */
        $user = $this->em->find(User::class, $this->user->id);

        $data = $user->getUserData();
        $data['success'] = true;

        $this->sendJson($data);
    }

}