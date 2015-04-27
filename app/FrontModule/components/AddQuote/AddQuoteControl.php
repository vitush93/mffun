<?php

namespace App\FrontModule\Components\AddQuote;

use App\FrontModule\Forms\AddQuoteForm;
use App\Libs\Utils;
use App\Model\Entities\Quote;
use App\Model\Entities\Subject;
use App\Model\Entities\Teacher;
use App\Model\Entities\User;
use App\Model\Repositories\QuoteRepository;
use App\Model\Services\AutocompleteService;
use Kdyby\Doctrine\EntityManager;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;

interface IAddQuoteControlFactory
{
    /** @return AddQuoteControl */
    function create();
}

/**
 * Class AddQuoteControl
 * @package App\FrontModule\Components\AddQuote
 */
class AddQuoteControl extends Control
{
    /** @var \Kdyby\Doctrine\EntityManager */
    private $em;

    /** @var \App\Model\Repositories\QuoteRepository */
    private $quoteRepository;

    /** @var \Kdyby\Doctrine\EntityDao */
    private $userDao;

    /** @var \Kdyby\Doctrine\EntityDao */
    private $subjectDao;

    /** @var AutocompleteService */
    private $autocompleteService;

    /** @var \Kdyby\Doctrine\EntityDao */
    private $teacherDao;

    public function __construct(EntityManager $entityManager, QuoteRepository $quoteRepository, AutocompleteService $autocompleteService)
    {
        parent::__construct();

        $this->quoteRepository = $quoteRepository;
        $this->em = $entityManager;
        $this->autocompleteService = $autocompleteService;
        $this->userDao = $entityManager->getDao(User::getClassName());
        $this->subjectDao = $entityManager->getDao(Subject::getClassName());
        $this->teacherDao = $entityManager->getDao(Teacher::getClassName());
    }

    public function render()
    {
        $this->template->setFile(__DIR__ . '/AddQuote.latte');
        $this->template->render();
    }

    /**
     * [AddQuoteForm]
     * Tries to add a new quote to the database.
     *
     * @param Form $form
     */
    public function processAddQuoteForm(Form $form)
    {
        $data = $form->getValues(true);

        $quote = new Quote($data['title'], $data['text']);

        // author & author's email
        if ($this->presenter->getUser()->isLoggedIn()) {
            /** @var User $author */
            $author = $this->userDao->find($this->presenter->getUser()->getId());
            $user_email = $author->getEmail();
        } else {
            $author = $this->userDao->findOneBy(array('username' => 'unknown'));
            $user_email = $data['user_email'];
        }
        $quote->setUser($author);
        $quote->setUserEmail($user_email);

        // set teacher, add new teacher if not exists
        if ($data['teacher']) {
            $teacher = $this->teacherDao->findOneBy(array('name' => $data['teacher']));
            if ($teacher == NULL) {
                $teacher = new Teacher($data['teacher']);
            }
            $quote->setTeacher($teacher);
        }

        // set subject, add new subject if not exists
        if ($data['subject']) {
            $subject = $this->subjectDao->findOneBy(array('name' => $data['subject']));
            if ($subject == NULL) {
                $subject = new Subject($data['subject']);
            }
            $quote->setSubject($subject);
        }

        Utils::safeExplodeByComma($data['tags']);

        // add new quote
        $this->quoteRepository->create($quote, $data['tags']);
        $this->em->flush();

        if ($quote->isApproved()) {
            $this->presenter->flashMessage('Citace byla přidána.', 'success');
        } else {
            $this->presenter->flashMessage('Citace byla odeslána ke schválení.', 'info');
        }

        $this->presenter->redirect('this');
    }

    /**
     *  Subjects for jquery autocomplete.
     */
    public function handleSubjectJson()
    {
        $this->presenter->sendJson($this->autocompleteService->getSubjectsAsArray());
    }

    /**
     * Tags for jquery autocomplete.
     */
    public function handleTagsJson()
    {
        $this->presenter->sendJson($this->autocompleteService->getTagsAsArray());
    }

    /**
     * Teacher for jquery autocomplete.
     */
    public function handleTeacherJson()
    {
        $this->presenter->sendJson($this->autocompleteService->getTeachersAsArray());
    }

    /**
     * AddQuoteForm factory.
     *
     * @return Form
     */
    protected function createComponentQuoteForm()
    {
        $form = new Form();

        $form->addText('title', 'Titulek (povinné)')->setRequired('Vyplňte prosím.');
        $form->addTextArea('text', 'Text citace (povinné)')->setRequired('Vyplňte prosím.');
        $form->addText('subject', 'Předmět');
        $form->addText('teacher', 'Vyučující');
        $form->addText('tags', 'Tagy')
            ->setAttribute('placeholder', 'analýza, kolej, karlín');
        $form->addText('user_email', 'Tvůj e-mail')
            ->addCondition(Form::FILLED)
            ->addRule(Form::EMAIL, 'Zadejte platnou e-mailovou adresu.');


        foreach ($form->getControls() as $control) {
            $control->getControlPrototype()->class('form-input');
        }

        // add class for subject autocomplete
        $orig_class = $form['subject']->getControlPrototype()->class;
        $form['subject']->getControlPrototype()->class = $orig_class . ' subject-autocomplete';

        // add class for teacher autocomplete
        $orig_class = $form['teacher']->getControlPrototype()->class;
        $form['teacher']->getControlPrototype()->class = $orig_class . ' teacher-autocomplete';

        // add class for tags autocomplete
        $orig_class = $form['tags']->getControlPrototype()->class;
        $form['tags']->getControlPrototype()->class = $orig_class . ' tags-autocomplete';

        $form->addSubmit('process', 'přidat citát');

        $form->onSuccess[] = $this->processAddQuoteForm;

        return $form;
    }
}