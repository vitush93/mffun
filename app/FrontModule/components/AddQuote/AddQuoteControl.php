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
use Nette\Application\BadRequestException;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use ReCaptcha\ReCaptcha;

interface IAddQuoteControlFactory
{
    /** @return AddQuoteControl */
    function create();
}

/**
 * Handles the add quote form.
 *
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
        $this->userDao = $entityManager->getRepository(User::getClassName());
        $this->subjectDao = $entityManager->getRepository(Subject::getClassName());
        $this->teacherDao = $entityManager->getRepository(Teacher::getClassName());
    }

    public function render()
    {
        $this->template->setFile(__DIR__ . '/AddQuote.latte');
        if ($this->presenter->user->isLoggedIn()) {
            $this->template->u = $this->em->find(User::class, $this->presenter->user->id);
        }
        $this->template->render();
    }

    private function recaptcha($response)
    {
        $recaptcha = new ReCaptcha(RECAPTCHA_SECRET);
        $resp = $recaptcha->verify($response, $_SERVER['REMOTE_ADDR']);

        return $resp->isSuccess();
    }

    /**
     * [AddQuoteForm]
     * Tries to add a new quote to the database.
     *
     * @param Form $form
     * @throws BadRequestException
     */
    public function processAddQuoteForm(Form $form)
    {
        $data = $form->getValues(true);

        if (!$this->presenter->user->isLoggedIn()) {
            if (!$this->recaptcha($_POST['g-recaptcha-response'])) {
                $this->presenter->flashMessage('Potvrď prosím, že nejsi robot.', 'info');

                return;
            }
        }

        $text = \HTMLPurifier::getInstance()->purify($data['text']);
        if (strlen($text) == 0) {
            $this->presenter->flashMessage('Nelze přidat citaci s prázdným textem.', 'error');
            return;
        }

        $quote = new Quote($text);

        // author & author's email
        if ($this->presenter->getUser()->isLoggedIn()) {
            /** @var User $author */
            $author = $this->userDao->find($this->presenter->getUser()->getId());
            $user_email = $author->getEmail();
        } else {
            $author = NULL;
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
     * @param callable $callable
     */
    public function modifyForm(callable $callable)
    {
        $callable($this['quoteForm']);
    }

    /**
     * @return Form
     */
    public function getForm()
    {
        return $this['quoteForm'];
    }

    /**
     * AddQuoteForm factory.
     *
     * @return Form
     */
    protected function createComponentQuoteForm()
    {
        $form = new Form();

        $form->addTextArea('text', 'Text citace (povinné)')
            ->addRule(Form::MAX_LENGTH, 'Text nesmí být delší než %d znaků.', 1000)
            ->setRequired('Vyplňte prosím.');
        $form->addText('subject', 'Předmět')
            ->addCondition(Form::FILLED)
            ->addRule(Form::MAX_LENGTH, 'Zadej méně než %d znaků.', 70);
        $form->addText('teacher', 'Vyučující')
            ->addCondition(Form::FILLED)
            ->addRule(Form::MAX_LENGTH, 'Zadej méně než %d znaků.', 35);
        $form->addText('tags', 'Tagy')
            ->addCondition(Form::FILLED)
            ->addRule(Form::MAX_LENGTH, 'Moc moc :(', 50);
        $form->addText('user_email', 'Tvůj e-mail')
            ->addCondition(Form::FILLED)
            ->addRule(Form::EMAIL, 'Zadejte platnou e-mailovou adresu.');

        $form['tags']->setAttribute('placeholder', 'analýza, kolej, karlín');

        foreach ($form->getControls() as $control) {
            $control->getControlPrototype()->class('uid-input');
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