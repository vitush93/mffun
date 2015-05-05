<?php

namespace App\FrontModule\Presenters;


use App\FrontModule\Components\CheckLdap\ILdapCheckControlFactory;
use App\Model\Entities\User;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Form;
use Nette\Http\Session;
use Nette\Http\SessionSection;
use Nette\Security\Passwords;
use Nette\Utils\ArrayHash;

class UsercpPresenter extends BasePresenter
{
    /** @var User */
    private $info;

    /** @persistent */
    public $setup = 'sum';

    /** @var ILdapCheckControlFactory @inject */
    public $ldapCheckControlFactory;

    /** @var Session @inject */
    public $session;

    /** @var SessionSection */
    public $section;

    protected function startup()
    {
        parent::startup();

        if (!$this->user->isLoggedIn()) throw new BadRequestException;

        $this->info = $this->template->info;
        $this->template->numq = $this->info->getQuotations()->count();
        $this->template->numc = $this->info->getComments()->count();
        $this->template->rank = $this->info->getCrank();
        $this->template->u = $this->info;

        $this->section = $this->session->getSection('mff');
    }

    public function beforeRender()
    {
        parent::beforeRender();

        if (isset($this->section->data)) {
            $u = $this->em->getRepository(User::class)->findOneBy(['mff' => $this->section->data->uid]);
            if ($u) {
                $this->flashMessage('Jiný uživatel již má účet propojený s UK těmito LDAP údaji.', 'error');
            } else {
                $this->info->setMff($this->section->data->uid);
                $this->em->flush();

                $this->flashMessage('Nyní jsi ověřený matfyzák!', 'success');
            }

            unset($this->section->data);
            $this->redirect('this');
        }

        $this->template->setup = $this->setup;

        $this['accForm']['username']->setDefaultValue($this->info->getUsername());
        $this['accForm']['name']->setDefaultValue($this->info->getName());
        $this['accForm']['email']->setDefaultValue($this->info->getEmail());

        $this['avatarForm']['avatar']->setDefaultValue($this->info->getAvatar());
    }

    protected function createComponentPassForm()
    {
        $form = new Form();

        $form->addPassword('old', 'Současné heslo')
            ->setRequired('Toto pole je povinné.');
        $form->addPassword('password', 'Nové Heslo')
            ->addRule(Form::MIN_LENGTH, 'Zadejte aspoň 6 znaků.', 6)
            ->setRequired('Toto pole je povinné.');
        $form->addPassword('password2', 'Nové heslo znovu')
            ->setRequired('Toto pole je povinné.')
            ->addRule(Form::EQUAL, 'Hesla se neshodují.', $form['password'])
            ->setOmitted();
        $form->addSubmit('process', 'Uložit')
            ->getControlPrototype()->class('button blue');

        $form->onSuccess[] = function (Form $form, ArrayHash $values) {
            $user = $this->em->find(User::class, $this->user->id);

            if (!Passwords::verify($values->old, $user->getPassword())) {
                $form->addError('Zadal jsi špatné současné heslo.');
                return;
            }

            $user->setPassword($values->password);
            $this->em->flush();

            $this->flashMessage('Heslo bylo změněno.', 'info');
        };

        return $form;
    }

    protected function createComponentAvatarForm()
    {
        $form = new Form();

        $form->addHidden('avatar', '')->getControlPrototype()->class('acc-avatar-input');
        $form->addSubmit('process', 'Uložit')
            ->getControlPrototype()->class('button blue');

        $form->onSuccess[] = function (Form $form, ArrayHash $values) {
            $user = $this->em->find(User::class, $this->user->id);
            $user->setAvatar($values->avatar);
            $this->em->flush();
            $this->flashMessage('Změny byly uloženy.', 'info');
        };

        return $form;
    }

    protected function createComponentAccForm()
    {
        $form = new Form();

        $form->addText('username', 'Login')
            ->setRequired('Toto pole je povinné.');
        $form->addText('name', 'Jméno');
        $form->addText('email', 'E-mail')
            ->setRequired('Toto pole je povinné.');
        $form->addSubmit('process', 'Uložit')
            ->getControlPrototype()->class('button blue');

        $form->onSuccess[] = function (Form $form, ArrayHash $values) {
            $user = $this->em->find(User::class, $this->user->id);

            $user->setUsername($values->username);
            $user->setName($values->name);
            $user->setEmail($values->email);

            try {
                $this->em->flush();
                $this->flashMessage('Změny byly uloženy.', 'info');
            } catch (UniqueConstraintViolationException $e) {
                $form->addError('Uživatel s tímto loginem nebo e-mailem již existuje.');
                $this->info = $this->em->find(User::class, $this->user->id);
                $this->template->info = $this->info;
            }
        };


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