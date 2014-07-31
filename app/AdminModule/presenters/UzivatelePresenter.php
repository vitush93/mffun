<?php

namespace App\AdminModule;

use App\Libs\BootstrapForm;
use App\Libs\DoctrineForm;
use App\Model\Entities\User;
use Kdyby\Doctrine\DuplicateEntryException;
use Kdyby\Doctrine\EntityManager;
use Nette\Application\UI\Form;
use App\Model\Repositories\UserRepository;

class UzivatelePresenter extends BasePresenter
{
    /** @var  IGridoUsersControlFactory @inject */
    public $gridoUsersControlFactory;

    /** @var UserRepository @inject */
    public $userRepository;

    public function actionDetail($id)
    {
        $this->template->info = $this->userRepository->find($id);
    }

    public function actionEdit($id)
    {
        $user = $this->userRepository->find($this->getParameter('id'));
        $this->setView('default');
        if ($this->isAjax()) {
            $this['newUserForm']
                ->setDefaults(array(
                    'username' => $user->getUsername(),
                    'role' => $user->getRole(),
                    'email' => $user->getEmail()
                ))
                ->setFormName('Upravit uživatele')
                ->show()
                ->refresh();
        }
    }

    public function actionDelete($id)
    {
        $this->userRepository->remove($id);

        $this->flashMessage('Uživatel byl smazán', 'info');
        $this->redirect('default');
    }

    public function handleModalNew()
    {
        $this['newUserForm']
            ->reset()
            ->show()
            ->refresh();
    }

    public function editUser(Form $form)
    {
        try {
            $values = $form->getValues();
            $user = $this->userRepository->find($this->getParameter('id'));

            $user->setUsername($values->username);
            $user->setRole($values->role);
            $user->setPassword($values->password);
            $user->setEmail($values->email);

            $this->userRepository->save($user);

            $this->flashMessage('Změny byly uloženy.', 'success');
            $this->redirect('default');
        } catch (DuplicateEntryException $e) {
            $this['newUserForm']->show();
            $form->addError('Uživatel s tímto uživatelským jménem nebo e-mailem již existuje.');
        }
    }

    public function newUser(Form $form)
    {
        try {
            $data = $form->getValues(true);

            $newuser = new User();
            $newuser->setUsername($data['username']);
            $newuser->setPassword($data['password']);
            $newuser->setEmail($data['email']);
            $newuser->setRole($data['role']);

            $this->userRepository->add($newuser);

            $this->flashMessage('Uživatel byl vytvořen.', 'success');
            $this->redirect('default');
        } catch (DuplicateEntryException $e) {
            $this['newUserForm']->show();
            $form->addError('Uživatel s tímto uživatelským jménem nebo e-mailem již existuje.');
        }
    }

    protected function createComponentNewUserForm()
    {
        $form = new Form();

        $form->addGroup('Přihlašovací údaje');
        $form->addText('username', 'Uživ. jméno')
            ->addRule(Form::PATTERN, 'Jsou povoleny pouze alfanumericke znaky', '^[a-zA-Z0-9]*$')
            ->addRule(Form::FILLED, 'Vyplňte prosím.')
            ->addRule(Form::MIN_LENGTH, 'Uživ. jméno musí mít alespoň %d znaky.', 4);

        $form->addPassword('password', 'Heslo')
            ->addCondition(Form::FILLED)
            ->addRule(Form::MIN_LENGTH, 'Heslo musí mít alespoň %d znaků', 6);
        $form->addPassword('password2', 'Heslo znovu')
            ->addRule(Form::EQUAL, 'Hesla se neshodují.', $form['password'])
            ->setOmitted();

        if (!$this->getParameter('id')) {
            $form['password']
                ->addRule(Form::MIN_LENGTH, 'Heslo musí mít alespoň %d znaků', 6)
                ->setRequired('Vyplňte prosím.');
        }

        $form->addGroup('Vlastnosti účtu');
        $form->addText('email', 'E-mail')
            ->addRule(Form::EMAIL, 'Zadejte platnou e-mailovou adresu.')
            ->setRequired('Vyplňte prosím.');
        $form->addSelect('role', 'Role', array('user' => 'Registrovaný', 'admin' => 'Admin'))
            ->setPrompt('-vyberte-')
            ->addRule(Form::FILLED, 'Vyplňte prosím.');

        $form->addGroup();
        $form->addSubmit('submit', 'Uložit');

        if (!$this->getParameter('id')) {
            $form->onSuccess[] = $this->newUser;
        } else {
            $form->onSuccess[] = $this->editUser;
        }

        return new ModalFormControl($form, 'Nový uživatel');
    }

    protected function createComponentGridoUsers()
    {
        return $this->gridoUsersControlFactory->create();
    }
}