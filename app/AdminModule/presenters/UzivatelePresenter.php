<?php

namespace App\AdminModule;

use App\AdminModule\forms\UserForm;
use App\Libs\BootstrapForm;
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

    /** @var EntityManager @inject */
    public $em;

    public function actionNew()
    {
        $this['userForm']->onSuccess[] = $this->newUser;
        $this['userForm']['password']
            ->addRule(Form::MIN_LENGTH, 'Heslo musí mít alespoň %d znaků', 6)
            ->setRequired('Vyplňte prosím.');
    }

    public function actionDetail($id)
    {
        $this->template->info = $this->userRepository->find($id);
    }

    public function actionEdit($id)
    {
        $this->setView('new');

        $user = $this->userRepository->find($id);

        $form = $this['userForm'];
        $form->onSuccess[] = $this->editUser;
        $form['password']
            ->addCondition(Form::FILLED)
            ->addRule(Form::MIN_LENGTH, 'Heslo musí mít alespoň %d znaků', 6);
        $form->bindEntity($user);
    }

    public function actionDelete($id)
    {
        $this->userRepository->remove($id);

        $this->flashMessage('Uživatel byl smazán', 'info');
        $this->redirect('default');
    }

    public function editUser(Form $form)
    {
        try {
            $values = $form->getValues();

            $user = $this->userRepository->find($this->getParameter('id'));

            $user->setUsername($values->username);
            $user->setRole($values->role);
            if (strlen($values->password) > 5) {
                $user->setPassword($values->password);
            }
            $user->setEmail($values->email);

            $this->userRepository->save($user);
            $this->em->flush();

            $this->flashMessage('Změny byly uloženy.', 'success');
            $this->redirect('default');
        } catch (DuplicateEntryException $e) {
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

            $this->userRepository->save($newuser);
            $this->em->flush();

            $this->flashMessage('Uživatel byl vytvořen.', 'success');
            $this->redirect('default');
        } catch (DuplicateEntryException $e) {
            $form->addError('Uživatel s tímto uživatelským jménem nebo e-mailem již existuje.');
        }
    }

    protected function createComponentUserForm()
    {
        $form = new UserForm();

        return BootstrapForm::makeBootstrap($form);
    }

    protected function createComponentGridoUsers()
    {
        return $this->gridoUsersControlFactory->create();
    }
}