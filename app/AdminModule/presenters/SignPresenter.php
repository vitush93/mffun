<?php

namespace App\AdminModule\Presenters;

use App\AdminModule\Forms\SignInForm;
use Nette,
    Model;

class SignPresenter extends BasePresenter
{

    public function renderDefault()
    {
        $this->template->pageTitle = 'Přihlášení';
        $this->template->pageDesc = 'přihlášení do administrace';
    }

    /**
     * SignInForm factory.
     *
     * @return SignInForm
     */
    protected function createComponentSignInForm()
    {
        $form = new SignInForm();

        $form->onSuccess[] = $this->signInFormSucceeded;
        return $form;
    }

    /**
     * [SignInForm]
     * Tries to authorize the user.
     *
     * @param Nette\Application\UI\Form $form
     */
    public function signInFormSucceeded(Nette\Application\UI\Form $form)
    {
        $values = $form->getValues();

        if ($values->remember) {
            $this->getUser()->setExpiration('14 days', false);
        } else {
            $this->getUser()->setExpiration('20 minutes', true);
        }

        try {
            $this->getUser()->login($values->username, $values->password);
            $this->redirect('Homepage:');
        } catch (Nette\Security\AuthenticationException $e) {
            $form->addError($e->getMessage(), 'danger');
        }
    }

}
