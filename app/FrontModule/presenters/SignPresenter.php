<?php

namespace App\FrontModule\Presenters;


use App\FrontModule\Components\CheckLdap\ILdapCheckControlFactory;
use App\Libs\BootstrapForm;
use App\Model\Entities\PasswordRecovery;
use App\Model\Entities\User;
use App\Model\Repositories\UserRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Form;
use Nette\Http\Session;
use Nette\Http\SessionSection;
use Nette\InvalidStateException;
use Nette\Mail\IMailer;
use Nette\Mail\Message;
use Nette\Utils\ArrayHash;

class SignPresenter extends BasePresenter
{
    /** @var Session @inject */
    public $session;

    /** @var SessionSection */
    public $section;

    /** @var IMailer @inject */
    public $mailer;

    /** @var UserRepository @inject */
    public $userRepository;

    /** @var ILdapCheckControlFactory @inject */
    public $ldapCheckControlFactory;

    protected function startup()
    {
        parent::startup();

        $this->section = $this->session->getSection('mff');
    }

    public function actionIn()
    {
        if ($this->user->isLoggedIn()) {
            $this->redirect('Homepage:default');
        }
    }

    public function actionRegister()
    {
        if (isset($this->section->data)) {
            $form = $this['registerForm'];

            $form['name']->setDefaultValue($this->section->data->name);
            $form['email']->setDefaultValue($this->section->data->email);

            $this->presenter->template->check = true;
        }
    }

    public function handleCancelLdap()
    {
        unset($this->section->data);
        $this->redirect('this');
    }

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
            $crank = 0;
            if (isset($this->section->data)) {
                $user = $this->em->getRepository(User::class)->findOneBy(['mff' => $this->section->data->uid]);
                if ($user) {
                    throw new InvalidStateException;
                }
                $crank = 5;
            }

            $auth = isset($this->section->data) ? $this->section->data->uid : NULL;
            $this->userRepository->createUser($values, $crank, $auth);
            $this->em->flush();

            unset($this->section->data);

            $this->flashMessage("Registrace proběhla úspěšně. Nyní se můžeš přihlásit.", "success");
            $this->redirect("Homepage:default");
        } catch (UniqueConstraintViolationException $e) {
            $form->addError("Uživatel s tímto uživatelským jménem nebo e-mailem již existuje.");
        } catch (InvalidStateException $e) {
            $form->addError('Jiný uživatel již má účet propojený s UK těmito LDAP údaji.');
            unset($this->section->data);
            unset($this->template->check);
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
        $mail->setFrom('MFFun <noreply@vithabada.cz>')
            ->addTo($rec->getEmail())
            ->setSubject('MFFun: Obnova hesla')
            ->setHtmlBody("
            Dobrý den,<br>
            na tuto e-mailovou adresu byl vyžádán odkaz pro obnovu hesla k účtu na MFFun. Pokud si chcete obnovit heslo klikněte na odkaz níže (platnost odkazu je 14 dní).
            <br><br>
            <a href=\"{$this->link('//Sign:recovery', $rec->getToken())}\">{$this->link('//Sign:recovery', $rec->getToken())}</a>
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

        $form->addText('email', 'E-mail')
            ->addRule(Form::EMAIL, 'Zadejte platnou e-mailovou adresu.')
            ->setRequired('Vyplňte prosím.')
            ->getControlPrototype()->class('form-input');

        $form->onSuccess[] = $this->recoverFormSucceeded;

        $form = BootstrapForm::makeBootstrap($form);
        foreach ($form->getControls() as $control) {
            $control->getControlPrototype()->class('form-input');
        }

        $form->addSubmit('process', 'Odeslat')
            ->getControlPrototype()->class('button blue');
        $form->getElementPrototype()->class = 'form-horizontal register-form';

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

        $form->addSubmit('process', 'Odeslat')
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

        $form->addText('username', 'Login')
            ->addRule(Form::MIN_LENGTH, 'Zadej aspoň %d znaky.', 3)
            ->addRule(Form::MAX_LENGTH, 'Maximální délka loginu je %d znaků.', 15)
            ->setRequired('Vyplňte prosím.')
            ->setOption('description', 'Login musí mít alespoň 3 znaky.');
        $form->addText('email', 'E-mail')
            ->addRule(Form::EMAIL, 'Zadejte platnou e-mailovou adresu.')
            ->setRequired('Vyplňte prosím.')
            ->setOption('description', 'E-mail pro oznámení. Nebudeme ti posílat spam.');
        $form->addPassword('password', 'Heslo')
            ->addRule(Form::MIN_LENGTH, 'Zadejte aspoň 6 znaků.', 6)
            ->setRequired('Vyplňte prosím.')
            ->setOption('description', 'Heslo musí mít alespoň 6 znaků.');
        $form->addPassword('password2', 'Heslo znovu')
            ->setRequired('Vyplňte prosím.')
            ->addRule(Form::EQUAL, 'Hesla se neshodují.', $form['password'])
            ->setOmitted()
            ->setOption('description', 'Zadej heslo znovu pro kontrolu.');
        $form->addText('name', 'Jméno')
            ->setOption('description', 'Tvoje celé jméno. Nepovinné pole.')
            ->addCondition(Form::FILLED)
            ->addRule(Form::MAX_LENGTH, 'Jméno nesmí být delší než %d znaků.', 35);

        $form->onSuccess[] = $this->registerFormSucceeded;

        $form = BootstrapForm::makeBootstrap($form);
        foreach ($form->getControls() as $control) {
            $control->getControlPrototype()->class('form-input');
        }

        $form->addSubmit('process', 'Registrovat')
            ->getControlPrototype()->class('button blue');
        $form->getElementPrototype()->class = 'form-horizontal register-form';

        return $form;
    }

    /**
     * @return \App\FrontModule\Components\CheckLdap\LdapCheckControl
     */
    protected function createComponentLdapCheck()
    {
        return $this->ldapCheckControlFactory->create();
    }
}