<?php

namespace App\FrontModule\Components\AddQuote;


use App\Libs\DoctrineForm;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;

class AddQuoteControl extends Control
{
    public function __construct()
    {
        parent::__construct();
    }

    public function render()
    {
        $this->template->setFile(__DIR__.'/AddQuote.latte');
        $this->template->render();
    }

    public function processAddQuoteForm(DoctrineForm $form)
    {
        dump($form->getValues());
        die;
    }

    protected function createComponentQuoteForm()
    {
        $form = new DoctrineForm();
        $form->addText('subject', 'Předmět')->setRequired('Vyplňte prosím.');
        $form->addText('teacher', 'Vyučující')->setRequired('Vyplňte prosím.');
        $form->addTextArea('text', 'Text citace')->setRequired('Vyplňte prosím.');
        $form->addText('date', 'Datum výroku')
            ->setDefaultValue(date('j.n.Y'))
            ->setRequired('Vyplňte prosím.');
        $form->addText('user_email','Tvůj e-mail')
            ->addCondition(Form::FILLED)
            ->addRule(Form::EMAIL, 'Zadejte platnou e-mailovou adresu.');
        $form->addSubmit('process', 'přidat citát');

        $form->onSuccess[] = $this->processAddQuoteForm;

        return $form;
    }
}

interface IAddQuoteControlFactory
{
    /** @return AddQuoteControl */
    function create();
}