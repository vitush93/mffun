<?php

namespace App\FrontModule\Components\AddQuote;

use App\FrontModule\Forms\AddQuoteForm;
use App\Model\Entities\User;
use App\Model\Repositories\QuoteRepository;
use Kdyby\Doctrine\EntityManager;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use App\Model\Entities\Quote;
use Nette\Utils\DateTime;
use App\Model\Entities\Teacher;
use App\Model\Entities\Subject;
use App\Libs\Utils;

/**
 *
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

    /** @var \Kdyby\Doctrine\EntityDao */
    private $teacherDao;

    public function __construct(EntityManager $entityManager, QuoteRepository $quoteRepository)
    {
        parent::__construct();

        $this->quoteRepository = $quoteRepository;
        $this->em = $entityManager;
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
    // TODO refactor
    public function processAddQuoteForm(Form $form)
    {
        $data = $form->getValues(true);

        $quote = new Quote();
        $quote->setDate(DateTime::from($data['date']));
        $quote->setText($data['text']);
        $quote->setUserEmail($data['user_email']);

        if ($this->presenter->getUser()->isLoggedIn()) {
            $author = $this->userDao->find($this->presenter->getUser()->getId());
        } else {
            $author = $this->userDao->findOneBy(array('username' => 'unknown'));
        }
        $quote->setUser($author);

        // set teacher, add new teacher if not exists
        $teacher = $this->teacherDao->findOneBy(array('name' => $data['teacher']));
        if ($teacher == NULL) {
            $teacher = new Teacher();
            $teacher->setName($data['teacher']);

            $this->teacherDao->save($teacher);
        }
        $quote->setTeacher($teacher);

        // set subject, add new subject if not exists
        $subject = $this->subjectDao->findOneBy(array('name' => $data['subject']));
        if ($subject == NULL) {
            $subject = new Subject();
            $subject->setName($data['subject']);

            $this->subjectDao->save($subject);
        }
        $quote->setSubject($subject);

        Utils::safeExplodeByComma($data['tags']);

        // add new quote
        $this->quoteRepository->create($quote, $data['tags']);
        $this->em->flush();

        $this->presenter->flashMessage('Citace byla přidána.', 'success');
        $this->presenter->redirect('this');
    }

    /**
     * AddQuoteForm factory.
     *
     * @return AddQuoteForm
     */
    protected function createComponentQuoteForm()
    {
        $form = new AddQuoteForm();
        $form->onSuccess[] = $this->processAddQuoteForm;

        return $form;
    }
}

interface IAddQuoteControlFactory
{
    /** @return AddQuoteControl */
    function create();
}