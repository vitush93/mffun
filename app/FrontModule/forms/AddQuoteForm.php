<?php

namespace App\FrontModule\Forms;


use Nette\Application\UI\Form;

class AddQuoteForm extends Form
{
    public function __construct()
    {
        $this->addText('subject', 'Předmět')->setRequired('Vyplňte prosím.');
        $this->addText('teacher', 'Vyučující')->setRequired('Vyplňte prosím.');
        $this->addTextArea('text', 'Text citace')->setRequired('Vyplňte prosím.');
        $this->addText('tags', 'Tagy')->setRequired('Vyplňte prosím.');
        $this->addText('date', 'Datum výroku')
            ->setDefaultValue(date('j.n.Y'))
            ->setRequired('Vyplňte prosím.');
        $this->addText('user_email', 'Tvůj e-mail')
            ->addCondition(Form::FILLED)
            ->addRule(Form::EMAIL, 'Zadejte platnou e-mailovou adresu.');
        $this->addSubmit('process', 'přidat citát');
    }
} 