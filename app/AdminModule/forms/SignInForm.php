<?php

namespace App\AdminModule\Forms;


use Nette\Application\UI\Form;

class SignInForm extends Form
{
    public function __construct()
    {
        $this->addText('username', 'Jméno:', 80, 50)
            ->setAttribute('placeholder', 'Uživatelské jméno')
            ->setRequired('zadejte své uživatelské jméno')
            ->getControlPrototype()->class('form-control');

        $this->addPassword('password', 'Heslo:', 80, 60)
            ->setAttribute('placeholder', 'Heslo')
            ->setRequired('zadejte své heslo')
            ->getControlPrototype()->class('form-control');

        $this->addCheckbox('remember', 'zapamatovat přihlášení');

        $this->addSubmit('send', 'Přihlásit')
            ->getControlPrototype()
            ->class('btn btn-lg btn-success btn-block');
        $this->getElementPrototype()->class('form-signin');
    }
} 