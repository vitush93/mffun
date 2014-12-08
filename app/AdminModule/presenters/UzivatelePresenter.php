<?php

namespace App\AdminModule\Presenters;

use App\AdminModule\Forms\NewUserForm;
use App\Model\Entities\User;
use Kdyby\Doctrine\EntityManager;
use App\Libs\BootstrapForm;
use App\Libs\DoctrineForm;
use Kdyby\Doctrine\DuplicateEntryException;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Form;
use App\Model\Repositories\UserRepository;

class UzivatelePresenter extends BasePresenter
{
    /** @var UserRepository @inject */
    public $userRepository;

    /** @var EntityManager @inject */
    public $em;

    private static function checkUser(User $user)
    {
        if (!$user) {
            throw new BadRequestException;
        }
    }

    public function actionDefault()
    {
        $this['newUserForm']->onSuccess[] = $this->newUser;
        $this['newUserForm']['password']
            ->addRule(Form::MIN_LENGTH, 'Heslo musí mít alespoň %d znaků', 6)
            ->setRequired('Vyplňte prosím.');
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

        $form->bindEntity($user);
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
            if (strlen($values->password) > 5) {
                $user->setPassword($values->password);
            }

            $this->em->persist($user);
            $this->em->flush();

            $this->flashMessage('Změny byly uloženy.', 'success');
            $this->redirect('default');
        } catch (DuplicateEntryException $e) {
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
            $new->setRole('user');

            $this->em->persist($new);
            $this->em->flush();

            $this->flashMessage('Uživatel byl vytvořen.', 'success');
            $this->redirect('default');
        } catch (DuplicateEntryException $e) {
            $form->addError('Uživatel s tímto uživatelským jménem již existuje.');
        }
    }

    /**
     * NewUserForm factory.
     *
     * @return Form
     */
    protected function createComponentNewUserForm()
    {
        $form = new NewUserForm();

        return BootstrapForm::makeBootstrap($form);
    }

}