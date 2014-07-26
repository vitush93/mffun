<?php

namespace App\AdminModule;

use App\Libs\IMAPMail;
use Nette,
    Model;

/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends Nette\Application\UI\Presenter
{
    /** @var IMAPMail */
    protected $imap;

    public function startup()
    {
        parent::startup();
        $this->detectAccess();
        $this->imap->start();
    }

    public function handleLogout()
    {
        $this->logout();
        $this->flashMessage('Byl jste odhlášen.', 'info');
        $this->redirect('Sign:default');
    }

    private function detectAccess()
    {
        if ($this->name !== 'Admin:Sign') {
            if (!$this->getUser()->isLoggedIn()) {
                if ($this->getUser()->getLogoutReason() === Nette\Security\IUserStorage::INACTIVITY) {
                    $this->flashMessage('Byl jste automaticky odhlášen (20 minut).', 'info');
                } else {
                    $this->flashMessage('Přihlašte se prosím.', 'info');
                }
                $this->redirect('Sign:default');
            } else {
                if (!$this->getUser()->isAllowed('Admin:*')) {
                    $this->flashMessage('Přístup odmítnut.', 'danger');
                    $this->logout();
                    $this->redirect('Sign:default');
                }
            }
        } else {
            if ($this->getUser()->isLoggedIn()) {
                if ($this->getUser()->isInRole('admin')) {
                    $this->redirect('Homepage:default');
                }
            }
        }
    }

    protected function setPageInfo($title, $description)
    {
        $this->template->pageTitle = $title;
        $this->template->pageDesc = $description;
    }

    public function logout()
    {
        if ($this->getUser()->isLoggedIn()) {
            $this->getUser()->logout();
        }
    }

    public static function activeRowToArray($data)
    {
        if (!($data instanceof Nette\Database\Table\ActiveRow)) {
            return null;
        }

        $return = array();
        foreach ($data as $key => $value) {
            $return[$key] = $value;
        }
        return $return;
    }

    /* New emails notification */

    public function beforeRender()
    {
        $this->template->eCount = $this->imap->getMessCount();
        $this->template->emails = $this->imap->getInbox();
    }

    public function injectImap(IMAPMail $imap)
    {
        $this->imap = $imap;
    }

    public function handleVisited($id)
    {
        $this->imap->emailVisited($id);
        $this->reconect("this");
    }
}
