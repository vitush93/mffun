<?php

namespace App\FrontModule\Presenters;


use App\Model\Entities\PasswordRecovery;
use App\Model\Entities\User;
use App\Model\Repositories\UserRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Form;
use Nette\Mail\IMailer;
use Nette\Mail\Message;
use Nette\Utils\ArrayHash;

class SignPresenter extends BasePresenter
{
    /** @var IMailer @inject */
    public $mailer;

    /** @var UserRepository @inject */
    public $userRepository;

    public function actionRecovery($id)
    {
        /** @var PasswordRecovery $rec */
        $rec = $this->em->getRepository(PasswordRecovery::class)->findOneBy(
            [
                'token' => $id,
                'valid' => TRUE,
            ]);
        if (!$rec) throw new BadRequestException;

        if (!$rec->isAlive()) {
            $this->flashMessage('Platnost tohoto odkazu vypršela (14 dní).', 'warning');
            $this->redirect('Sign:recover');
        }

        $this['recoveryForm']['token']->setDefaultValue($rec->getToken());

        $this->template->step3 = TRUE;
        $this->setView('recover');
    }

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
        // validate user
        /** @var User $user */
        $user = $this->em->getRepository(User::class)->findOneBy(['email' => $values->email]);
        if (!$user) {
            $form->addError('Uživatel s tímto e-mailem neexistuje.');
            return;
        }

        // create password recovery
        $rec = new PasswordRecovery($values->email);
        $this->em->persist($rec);
        $this->em->flush();

        // send e-mail
        $mail = new Message;
        $mail->setFrom('MFFun <noreply@beinshop.cz>')
            ->addTo($rec->getEmail())
            ->setSubject('Obnova hesla')
            ->setHtmlBody("
            Dobrý den,<br>
            na tuto e-mailovou adresu byl vyžádán odkaz pro obnovu hesla k účtu na MFFun. Pokud si chcete obnovit heslo klikněte na odkaz níže (platnost odkazu je 14 dní).
            <br><br>
            <a href=\"{$this->link('Sign:recovery', $rec->getToken())}\">{$this->link('Sign:recovery', $rec->getToken())}</a>
            ");
        $this->mailer->send($mail);

        $this->template->step2 = true;
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

        $form->onSuccess[] = $this->recoverFormSucceeded;
        $form->addProtection();

        return $form;
    }

    /**
     * RecoveryForm factory.
     *
     * @return Form
     */
    protected function createComponentRecoveryForm()
    {
        $form = new Form();

        $form->addPassword('password', 'Nové heslo')
            ->addRule(Form::MIN_LENGTH, 'Zadejte aspoň 6 znaků.', 6)
            ->setRequired('Vyplňte prosím.');
        $form->addPassword('password2', 'Heslo znovu')
            ->setRequired('Vyplňte prosím.')
            ->addRule(Form::EQUAL, 'Hesla se neshodují.', $form['password'])
            ->setOmitted();
        $form->addHidden('token', '');

        foreach ($form->getControls() as $control) {
            $control->getControlPrototype()->class('form-input');
        }

        $form->addSubmit('process', 'Registrovat')
            ->getControlPrototype()->class('button blue');

        $form->onSuccess[] = function (Form $form, ArrayHash $values) {
            /** @var PasswordRecovery $rec */
            $rec = $this->em->getRepository(PasswordRecovery::class)->findOneBy(['token' => $values->token]);
            $rec->invalidate();
            $user = $this->em->getRepository(User::class)->findOneBy(['email' => $rec->getEmail()]);
            $user->setPassword($values->password);
            $this->em->flush();

            $this->redirect('Homepage:default');
            $this->flashMessage('Vaše heslo bylo změněno.', 'info');
        };
        $form->addProtection();

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