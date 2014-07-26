<?php

namespace App\FrontModule;

use Nette,
    Model;

/**
 * Sign in/out presenters.
 */
class SignPresenter extends BasePresenter {

    public function startup() {
        parent::startup();
        if($this->getUser()->isLoggedIn() && $this->action !== 'out'){
            $this->redirect('Homepage:default');
        }
    }
    
    /**
     * Sign-in form factory.
     * @return Nette\Application\UI\Form
     */
    protected function createComponentSignInForm() {
        $form = new Nette\Application\UI\Form;
        $form->addText('username', 'Jmeno:')
                ->setRequired('Vyplnte.');

        $form->addPassword('password', 'Heslo:')
                ->setRequired('Vyplnte');

        $form->addCheckbox('remember', 'zapamatovat prihlaseni');

        $form->addSubmit('send', 'Prihlasit');

        // call method signInFormSucceeded() on success
        $form->onSuccess[] = $this->signInFormSucceeded;
        
        $form->addProtection();
        return $form;
    }

    public function signInFormSucceeded($form) {
        $values = $form->getValues();

        if ($values->remember) {
            $this->getUser()->setExpiration('14 days', FALSE);
        } else {
            $this->getUser()->setExpiration('20 minutes', TRUE);
        }

        try {
            $this->getUser()->login($values->username, $values->password);
            $this->redirect('Homepage:');
        } catch (Nette\Security\AuthenticationException $e) {
            $form->addError($e->getMessage());
        }
    }

    public function actionOut() {
        $this->getUser()->logout();
        $this->flashMessage('Byl jste odhlasen.', 'info');
        $this->redirect('Homepage:default');
    }

}
