<?php

namespace App\FrontModule\Forms;

use Nette\Application\UI\Form;

class LoginForm extends Form
{
    public function __construct()
    {
        $this->addText('username', 'login');
        $this->addPassword('password', 'heslo');
        $this->addCheckbox('remember', '')->setDefaultValue(true);
        $this->addSubmit('process', 'přihlásit');
    }

}