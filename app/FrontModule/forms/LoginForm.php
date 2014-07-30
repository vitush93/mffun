<?php

namespace App\FrontModule\Forms;

use Nette\Application\UI\Form;

class LoginForm extends Form
{
    public function __construct()
    {
        $this->addText('username', '');
        $this->addPassword('password', '');
        $this->addCheckbox('remember', '')->setDefaultValue(true);
        $this->addSubmit('process', 'přihlásit');
    }

}