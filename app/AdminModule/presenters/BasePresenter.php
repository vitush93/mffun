<?php

namespace App\AdminModule\Presenters;

use App\Model\Entities\User;
use Kdyby\Doctrine\EntityManager;
use Nette\Application\UI\Presenter;
use Nette\Security\IUserStorage;

class BasePresenter extends Presenter
{
    /** @var EntityManager @inject */
    public $em;

    public function handleLogout()
    {
        $this->logout();

        $this->redirect('Sign:default');
    }

    public function beforeRender()
    {
        if ($this->user->isLoggedIn()) {
            $this->template->userInfo = $this->em->find(User::getClassName(), $this->getUser()->getId());
        }
    }

    protected function startup()
    {
        parent::startup();

        $this->detectAccess();
    }

    /**
     * Detects if user is logged in and/or has access to the administration.
     */
    private function detectAccess()
    {
        if ($this->name !== 'Admin:Sign') {
            if (!$this->getUser()->isLoggedIn()) {
                if ($this->getUser()->getLogoutReason() === IUserStorage::INACTIVITY) {
                    $this->flashMessage('Byl jste automaticky odhlášen (20 minut).', 'info');
                } else {
                    $this->flashMessage('Přihlašte se prosím.', 'info');
                }
                $this->redirect('Sign:default');
            } else {
                if (!$this->getUser()->isAllowed($this->name)) {
                    $this->flashMessage('Přístup odmítnut.', 'danger');
                    $this->logout();
                    $this->redirect('Sign:default');
                }
            }
        } else {
            if ($this->getUser()->isLoggedIn()) {
                if ($this->getUser()->isInRole(User::ROLE_ADMIN) || $this->getUser()->isInRole(User::ROLE_MODERATOR)) {
                    $this->redirect('Homepage:default');
                }
            }
        }
    }

    /**
     * Logs out the user.
     */
    public function logout()
    {
        if ($this->getUser()->isLoggedIn()) {
            $this->getUser()->logout();
        }
    }
}