<?php

namespace App\AdminModule\forms;


use App\Libs\DoctrineForm;
use App\Model\Entities\User;
use Nette\Application\UI\Form;

class UserForm extends DoctrineForm
{
    public function __construct()
    {
        $this->addGroup('Přihlašovací údaje');
        $this->addText('username', 'Uživ. jméno')
            ->addRule(Form::PATTERN, 'Jsou povoleny pouze alfanumericke znaky', '^[a-zA-Z0-9]*$')
            ->addRule(Form::FILLED, 'Vyplňte prosím.')
            ->addRule(Form::MIN_LENGTH, 'Uživ. jméno musí mít alespoň %d znaky.', 4);

        $this->addPassword('password', 'Heslo');
        $this->addPassword('password2', 'Heslo znovu')
            ->addRule(Form::EQUAL, 'Hesla se neshodují.', $this['password'])
            ->setOmitted();

        $this->addGroup('Vlastnosti účtu');
        $this->addText('email', 'E-mail')
            ->addRule(Form::EMAIL, 'Zadejte platnou e-mailovou adresu.')
            ->setRequired('Vyplňte prosím.');
        $this->addSelect('role', 'Role', array(User::ROLE_USER => 'Registrovaný', User::ROLE_MODERATOR => 'Moderátor', User::ROLE_ADMIN => 'Admin'))
            ->setPrompt('-vyberte-')
            ->addRule(Form::FILLED, 'Vyplňte prosím.');

        $this->addGroup();
        $this->addSubmit('submit', 'Uložit');
    }
} 