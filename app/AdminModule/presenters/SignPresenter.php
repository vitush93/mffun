<?php

namespace App\AdminModule;

use Nette,
    Model;

/**
 * Sign in/out presenters.
 */
class SignPresenter extends BasePresenter
{

    public function renderDefault()
    {
        $this->template->pageTitle = 'Přihlášení';
        $this->template->pageDesc = 'přihlášení do administrace';
    }

    /**
     * Sign-in form factory.
     *
     * @return Nette\Application\UI\Form
     */
    protected function createComponentSignInForm()
    {
        $form = new Nette\Application\UI\Form;
        $form->addText('username', 'Jméno:', 80, 50)
            ->setAttribute('placeholder', 'uživatelské jméno')
            ->setRequired('zadejte své uživatelské jméno')
            ->getControlPrototype()->class('form-control');

        $form->addPassword('password', 'Heslo:', 80, 60)
            ->setAttribute('placeholder', 'heslo')
            ->setRequired('zadejte své heslo')
            ->getControlPrototype()->class('form-control');

        $form->addCheckbox('remember', 'zapamatovat si mě');

        $form->addSubmit('send', 'přihlásit')
            ->getControlPrototype()
            ->class('btn btn-lg btn-success btn-block');
        $form->getElementPrototype()->class('form-signin');
        // call method signInFormSucceeded() on success
        $form->onSuccess[] = $this->signInFormSucceeded;
        return $form;
    }

    public function signInFormSucceeded($form)
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
            $this->flashMessage($e->getMessage(), 'danger');
        }
    }

    public function actionOut()
    {
        $this->getUser()->logout();
        $this->flashMessage('You have been signed out.');
        $this->redirect('default');
    }

}
