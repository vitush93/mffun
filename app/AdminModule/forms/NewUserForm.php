<?php

namespace App\AdminModule\Forms;


use App\Libs\DoctrineForm;
use Nette\Application\UI\Form;

class NewUserForm extends DoctrineForm
{
    public function __construct()
    {
        $this->addText('username', 'Uživ. jméno')
            ->addRule(Form::PATTERN, 'Jsou povoleny pouze alfanumericke znaky', '^[a-zA-Z0-9]*$')
            ->addRule(Form::FILLED, 'Vyplňte prosím.')
            ->addRule(Form::MIN_LENGTH, 'Uživ. jméno musí mít alespoň %d znaky.', 4);

        $this->addPassword('password', 'Heslo');

        $this->addPassword('password2', 'Heslo znovu')
            ->addRule(Form::EQUAL, 'Hesla se neshodují.', $this['password'])
            ->setOmitted();

        $this->addSubmit('submit', 'Uložit');
    }
} 