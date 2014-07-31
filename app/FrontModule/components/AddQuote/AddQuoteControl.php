<?php

namespace App\FrontModule\Components\AddQuote;

use App\Libs\DoctrineForm;
use App\Model\Repositories\QuoteRepository;
use App\Model\Repositories\SubjectRepository;
use App\Model\Repositories\TeacherRepository;
use App\Model\Repositories\UserRepository;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use App\Model\Entities\Subject;
use App\Model\Entities\Quote;
use App\Model\Entities\Teacher;
use Nette\Utils\DateTime;


class AddQuoteControl extends Control
{
    /** @var \App\Model\Repositories\TeacherRepository */
    private $teacherRepository;

    /** @var \App\Model\Repositories\SubjectRepository */
    private $subjectRepository;

    /** @var \App\Model\Repositories\QuoteRepository */
    private $quoteRepository;

    /** @var \App\Model\Repositories\UserRepository */
    private $userRepository;

    public function __construct(
        TeacherRepository $teacherRepository,
        SubjectRepository $subjectRepository,
        QuoteRepository $quoteRepository,
        UserRepository $userRepository)
    {
        parent::__construct();

        $this->teacherRepository = $teacherRepository;
        $this->subjectRepository = $subjectRepository;
        $this->quoteRepository = $quoteRepository;
        $this->userRepository = $userRepository;
    }

    public function render()
    {
        $this->template->setFile(__DIR__ . '/AddQuote.latte');
        $this->template->render();
    }

    public function processAddQuoteForm(DoctrineForm $form)
    {
        $data = $form->getValues(true);

        $quote = new Quote();
        $quote->setDate(DateTime::from($data['date']));
        $quote->setText($data['text']);
        $quote->setUserEmail($data['user_email']);

        if ($this->presenter->getUser()->isLoggedIn()) {
            $quote->setUser($this->userRepository->find($this->presenter->getUser()->getId()));
        } else {
            $quote->setUser($this->userRepository->findOneByUsername('unknown'));
        }

        // set teacher, add new teacher if not exists
        $teacher = $this->teacherRepository->findOneByName($data['teacher']);
        if ($teacher === NULL) {
            $teacher = new Teacher();
            $teacher->setName($data['teacher']);
            $quote->setTeacher($this->teacherRepository->add($teacher));
        } else {
            $quote->setTeacher($teacher);
        }

        // set subject, add new subject if not exists
        $subject = $this->subjectRepository->findOneByName($data['subject']);
        if ($subject === NULL) {
            $subject = new Subject();
            $subject->setName($data['subject']);
            $quote->setSubject($this->subjectRepository->add($subject));
        } else {
            $quote->setSubject($subject);
        }

        // add new quote
        $this->quoteRepository->add($quote);

        $this->presenter->flashMessage('Citace byla přidána.', 'success');
        $this->presenter->redirect('this');
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
        $form->addText('user_email', 'Tvůj e-mail')
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