<?php

namespace App\FrontModule\Presenters;


use App\Model\Repositories\UserRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Kdyby\Doctrine\EntityManager;
use Nette\Application\UI\Form;
use Nette\Application\UI\Presenter;

class SignPresenter extends Presenter
{
    /** @var EntityManager @inject */
    public $em;

    /** @var UserRepository @inject */
    public $userRepository;

    use BasePresenterTrait;

    /**
     * [RegisterForm]
     * Attempts to add a new user to the database.
     *
     * @param Form $form
     * @param $values
     */
    public function registerFormSucceeded(Form $form, $values)
    {
        try {
            $this->userRepository->createUser($values);
            $this->em->flush();

            $this->flashMessage("Registrace proběhla úspěšně. Nyní se můžeš přihlásit.", "success");
            $this->redirect("Homepage:default");
        } catch (UniqueConstraintViolationException $e) {
            $this['registerForm']->addError("Uživatel s tímto uživatelským jménem nebo e-mailem již existuje.");
        }
    }

    /**
     * [RecoverForm]
     * Sends an e-mail with password recovery link & token.
     *
     * @param Form $form
     * @param $values
     */
    public function recoverFormSucceeded(Form $form, $values)
    {
        // TODO recovery function
        dump($values);
        die;
    }

    /**
     * RecoverForm factory.
     *
     * @return Form
     */
    protected function createComponentRecoverForm()
    {
        $form = new Form();

        $form->addText('email', 'E-mailová adresa')
            ->addRule(Form::EMAIL, 'Zadejte platnou e-mailovou adresu.')
            ->setRequired('Vyplňte prosím.')
            ->getControlPrototype()->class('form-input');
        $form->addSubmit('process', 'Odeslat')
            ->getControlPrototype()->class('button blue');

        return $form;
    }

    /**
     * RegisterForm factory.
     *
     * @return Form
     */
    protected function createComponentRegisterForm()
    {
        $form = new Form();

        $form->addText('username', 'Uživatelské jméno')
            ->addRule(Form::MIN_LENGTH, 'Zadejte aspoň 3 znaky.', 3)
            ->setRequired('Vyplňte prosím.');
        $form->addText('email', 'E-mail')
            ->addRule(Form::EMAIL, 'Zadejte platnou e-mailovou adresu.')
            ->setRequired('Vyplňte prosím.');
        $form->addPassword('password', 'Heslo')
            ->addRule(Form::MIN_LENGTH, 'Zadejte aspoň 6 znaků.', 6)
            ->setRequired('Vyplňte prosím.');
        $form->addPassword('password2', 'Heslo znovu')
            ->setRequired('Vyplňte prosím.')
            ->addRule(Form::EQUAL, 'Hesla se neshodují.', $form['password'])
            ->setOmitted();
        $form->addText('name', 'Jméno');

        // TODO avatar upload

        foreach ($form->getControls() as $control) {
            $control->getControlPrototype()->class('form-input');
        }

        $form->addSubmit('process', 'Registrovat')
            ->getControlPrototype()->class('button blue');

        $form->onSuccess[] = $this->registerFormSucceeded;
        $form->addProtection();

        return $form;
    }
}