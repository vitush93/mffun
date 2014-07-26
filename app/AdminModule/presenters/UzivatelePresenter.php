<?php

namespace App\AdminModule;

use App\Libs\BootstrapForm;
use App\Libs\DoctrineForm;
use App\Model\Entities\User;
use App\Model\UserManager;
use Kdyby\Doctrine\EntityManager;
use Nette\Application\UI\Form;

class UzivatelePresenter extends BasePresenter
{
    private $gridoUsersControlFactory;

    /** @var UserManager */
    private $userManager;

    /** @var EntityManager */
    private $entityManager;

    public function actionDetail($id)
    {
        $this->template->info = $this->entityManager->getDao(User::getClassName())->find($id);
    }

    public function actionEdit($id)
    {
        $user = $this->entityManager->getDao(User::getClassName())->find($this->getParameter('id'));
        $this->setView('default');
        if ($this->isAjax()) {
            $this['newUserForm']
                ->setDefaults(array(
                    'username' => $user->getUsername(),
                    'role' => $user->getRole()
                ))
                ->setFormName('Upravit uživatele')
                ->show()
                ->refresh();
        }
    }

    public function actionDelete($id)
    {
        $this->entityManager->remove(
            $this->entityManager->getDao(User::getClassName())->find($id)
        );
        $this->entityManager->flush();

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
            $user = $this->entityManager->getDao(User::getClassName())->find($this->getParameter('id'));
            $user->setUsername($values->username);
            $user->setRole($values->role);
            $user->setPassword($values->password);
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $this->flashMessage('Změny byly uloženy.', 'success');
            $this->redirect('default');
        } catch (\PDOException $e) {
            $this['newUserForm']->show();
            if ($e->getCode() == 23000) {
                $form->addError('Uživatel s tímto uživatelským jménem již existuje.');
            } else {
                throw $e;
            }
        }
    }

    public function newUser(Form $form)
    {
        try {
            $data = $form->getValues(true);
            $this->userManager->add($data);

            $this->flashMessage('Uživatel byl vytvořen.', 'success');
            $this->redirect('default');
        } catch (\PDOException $e) {
            $this['newUserForm']->show();
            if ($e->getCode() == 23000) {
                $form->addError('Uživatel s tímto uživatelským jménem již existuje.');
            } else {
                throw $e;
            }
        }
    }

    protected function createComponentDoctrineForm()
    {
        $form = new DoctrineForm();

        $form->addGroup('User credentials');
        $form->addText('username', 'Username')->setRequired();
        $form->addPassword('password', 'Password')->setRequired();
        $form->addSelect('role', 'Role', array(
            'admin' => 'Admin',
            'user' => 'User'
        ))->setPrompt('-vyberte-')->setRequired();

        $form->addGroup('Submit');
        $form->addSubmit('process', 'Submit');

        $form->onSuccess[] = $this->newUser;

        return BootstrapForm::makeBootstrap($form);
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

    public function injectGridoUsersControl(IGridoUsersControlFactory $grido)
    {
        $this->gridoUsersControlFactory = $grido;
    }

    public function injectUserManager(UserManager $userManager)
    {
        $this->userManager = $userManager;
    }

    public function injectEntityManager(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }
}