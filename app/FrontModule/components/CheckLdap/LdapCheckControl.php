<?php

namespace App\FrontModule\Components\CheckLdap;

use App\Libs\Utils;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Http\Session;
use Nette\Http\SessionSection;
use Nette\Utils\ArrayHash;
use Vitush\Ldap;
use Vitush\LdapException;
use Vitush\LdapSearchResult;

interface ILdapCheckControlFactory
{
    /** @return LdapCheckControl */
    function create();
}

class LdapCheckControl extends Control
{
    /** @var SessionSection */
    public $section;

    public function __construct(Session $session)
    {
        $this->section = $session->getSection('mff');
    }

    public function render()
    {
        $this->template->setFile(__DIR__ . '/LdapCheck.latte');

        $this->template->render();
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function ldapCheck(Form $form, ArrayHash $values)
    {
        if (!$values->uid) {
            $this->presenter->flashMessage('Uvedená identita neexistuje.', 'danger');

            return;
        }

        try {
            try {
                $x = Utils::getMffLdapNumericUid($values->uid);

                $ukco = $x['ukco'];

                /** @var LdapSearchResult $result */
                $result = $x['result'];
            } catch (LdapException $e) {
                $this->presenter->flashMessage('Uvedená identita neexistuje.', 'info');

                return;
            }

            // authenticate user with UKCO and given password
            $ldap = new Ldap("ldaps://ldap.cuni.cz");
            $r = $ldap->connect()
                ->bind("cunipersonalid={$ukco},ou=people,dc=cuni,dc=cz", $values->password);
            if (!$r) {
                $this->presenter->flashMessage('Došlo k chybě (špatné heslo?).', 'warning');

                return;
            }

            $this->section->data = new ArrayHash();

            $this->section->data->name = $result->get('cn');
            $this->section->data->email = $result->get('mail');
            $this->section->data->uid = $ukco;

            $this->presenter->redirect('this');
        } catch (LdapException $e) {
            $this->presenter->flashMessage('K LDAPu se nepodařilo připojit.', 'danger');
            $this->presenter->redirect('this');
        }
    }

    protected function createComponentLdapForm()
    {
        $form = new Form();

        $form->addText('uid', 'UKČO nebo login do SISu')
            ->setRequired('Vyplňte prosím.');
        $form->addPassword('password', 'Heslo')
            ->setRequired('Vyplňte prosím.');
        $form->addSubmit('process', 'Ověřit');

        $form->onSuccess[] = $this->ldapCheck;

        return $form;
    }
}