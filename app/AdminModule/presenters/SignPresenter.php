<?php

namespace App\AdminModule\Presenters;

use Model;
use Nette;
use Nette\Application\UI\Form;
use Nette\Application\UI\Presenter;

class SignPresenter extends Presenter
{

    use BasePresenterTrait;

    public function renderDefault()
    {
        $this->template->pageTitle = 'Přihlášení';
        $this->template->pageDesc = 'přihlášení do administrace';
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

    /**
     * SignInForm factory.
     *
     * @return Form
     */
    protected function createComponentSignInForm()
    {
        $form = new Form();

        $form->addText('username', 'Jméno:', 80, 50)
            ->setAttribute('placeholder', 'Uživatelské jméno')
            ->setRequired('zadejte své uživatelské jméno')
            ->getControlPrototype()->class('form-control');

        $form->addPassword('password', 'Heslo:', 80, 60)
            ->setAttribute('placeholder', 'Heslo')
            ->setRequired('zadejte své heslo')
            ->getControlPrototype()->class('form-control');

        $form->addCheckbox('remember', 'zapamatovat přihlášení');

        $form->addSubmit('send', 'Přihlásit')
            ->getControlPrototype()
            ->class('btn btn-lg btn-success btn-block');
        $form->getElementPrototype()->class('form-signin');

        $form->onSuccess[] = $this->signInFormSucceeded;
        return $form;
    }

}
