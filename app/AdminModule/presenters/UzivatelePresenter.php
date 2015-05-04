<?php

namespace App\AdminModule\Presenters;

use App\Libs\BootstrapForm;
use App\Libs\DoctrineForm;
use App\Model\Entities\User;
use App\Model\Repositories\UserRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Form;

class UzivatelePresenter extends BasePresenter
{
    /** @var UserRepository @inject */
    public $userRepository;

    public function actionDefault()
    {
        $this['newUserForm']->onSuccess[] = $this->newUser;
        $this['newUserForm']['password']
            ->addRule(Form::MIN_LENGTH, 'Heslo musí mít alespoň %d znaků', 6)
            ->setRequired('Vyplňte prosím.');
    }

    public function actionBan($id)
    {
        $user = $this->userRepository->find($id);
        self::checkUser($user);
        if ($user->getRole() != User::ROLE_USER) throw new BadRequestException;

        if ($user->isBanned()) {
            $user->setBan(NULL);
        } else {
            $admin_email = $this->userRepository->find($this->user->id)->getEmail();
            $user->setBan($admin_email);
        }

        $this->em->flush();

        $this->flashMessage("Uživatel {$user->getUsername()} byl zabanován/odbanován.", 'info');
        $this->redirect('default');
    }

    public function actionEdit($id)
    {
        $user = $this->userRepository->find($id);
        self::checkUser($user);

        $this->setView('default');

        /** @var DoctrineForm $form */
        $form = $this['newUserForm'];
        $form->onSuccess[] = $this->editUser;
        $form['password']
            ->addCondition(Form::FILLED)
            ->addRule(Form::MIN_LENGTH, 'Heslo musí mít alespoň %d znaků', 6);
        if (!$form->isAnchored() || !$form->isSubmitted()) {
            $form['username']->setValue($user->getUsername());
            $form['email']->setValue($user->getEmail());
            $form['role']->setValue($user->getRole());
        }
    }

    private static function checkUser(User $user)
    {
        if (!$user) {
            throw new BadRequestException;
        }
    }

    public function actionDetail($id)
    {
        $user = $this->userRepository->find($id);
        self::checkUser($user);

        $this->template->info = $user;
    }

    public function actionDelete($id)
    {
        if ($id == $this->user->id) {
            $this->flashMessage('Nelze smazat sebe sama.', 'danger');
            $this->redirect('default');
        } else {
            $this->userRepository->remove($id);

            $this->flashMessage('Uživatel byl smazán', 'info');
            $this->redirect('default');
        }
    }

    public function renderDefault()
    {
        $this->template->users = $this->userRepository->findAll();
    }

    /**
     * [NewUserForm]
     * Try to edit user details.
     *
     * @param Form $form
     */
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

            $this->em->flush();

            $this->flashMessage('Změny byly uloženy.', 'success');
            $this->redirect('default');
        } catch (UniqueConstraintViolationException $e) {
            $form->addError('Uživatel s tímto uživatelským jménem již existuje.');
        }
    }

    /**
     * [NewUserForm]
     * Try to add a new user to the database.
     *
     * @param Form $form
     */
    public function newUser(Form $form)
    {
        try {
            $data = $form->getValues();

            $new = new User();
            $new->setUsername($data->username);
            $new->setPassword($data->password);
            $new->setEmail($data->email);
            $new->setRole($data->role);

            $this->em->persist($new);
            $this->em->flush();

            $this->flashMessage('Uživatel byl vytvořen.', 'success');
            $this->redirect('default');
        } catch (UniqueConstraintViolationException $e) {
            $form->addError('Uživatel s tímto uživatelským jménem nebo e-mailem již existuje.');
        }
    }

    /**
     * NewUserForm factory.
     *
     * @return Form
     */
    protected function createComponentNewUserForm()
    {
        $form = new Form();

        $form->addText('username', 'Uživ. jméno')
            ->addRule(Form::PATTERN, 'Jsou povoleny pouze alfanumericke znaky', '^[a-zA-Z0-9]*$')
            ->addRule(Form::FILLED, 'Vyplňte prosím.')
            ->addRule(Form::MIN_LENGTH, 'Uživ. jméno musí mít alespoň %d znaky.', 4);
        $form->addText('email', 'E-mail')
            ->setRequired('Vyplňte prosím.')
            ->addRule(Form::EMAIL, 'Zadejte platný e-mail');
        $form->addSelect('role', 'Role', [
            User::ROLE_USER => User::ROLE_USER,
            User::ROLE_MODERATOR => User::ROLE_MODERATOR,
            User::ROLE_ADMIN => User::ROLE_ADMIN
        ])->setPrompt('-vyberte-')->setRequired('Vyberte.');

        $form->addPassword('password', 'Heslo');

        $form->addPassword('password2', 'Heslo znovu')
            ->addRule(Form::EQUAL, 'Hesla se neshodují.', $form['password'])
            ->setOmitted();

        $form->addSubmit('submit', 'Uložit');

        return BootstrapForm::makeBootstrap($form);
    }

}