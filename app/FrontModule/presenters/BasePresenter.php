<?php

namespace App\FrontModule\Presenters;

use App\FrontModule\Components\AddQuote\IAddQuoteControlFactory;
use App\FrontModule\Forms\LoginForm;
use App\Model\Entities\User;
use App\Model\Repositories\QuoteRepository;
use App\Model\Services\AutocompleteService;
use Doctrine\ORM\Query;
use Kdyby\Doctrine\EntityManager;
use Model;
use Nette;
use Nette\Application\UI\Form;
use Nette\Application\UI\Presenter;

/**
 * Base presenter for all application's presenter classes.
 *
 * Class BasePresenter
 * @package App\FrontModule\Presenters
 */
class BasePresenter extends Presenter
{
    /** @var IAddQuoteControlFactory @inject */
    public $addQuoteFactory;

    /** @var QuoteRepository @inject */
    public $quoteRepository;

    /** @var AutocompleteService @inject */
    public $autocompleteService;

    /** @var EntityManager @inject */
    public $em;

    /**
     * Fetch user info if user is logged in.
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     */
    protected function startup()
    {
        parent::startup();

        if ($this->user->isLoggedIn()) {
            /** @var User $user */
            $user = $this->em->find(User::class, $this->user->id);
            $this->template->info = $user;
        }
    }

    /**
     * Fetch global template data.
     */
    public function beforeRender()
    {
        if ($this->isAjax()) return; // skip for ajax requests

        $template = $this->template;
        $template->tags = $this->quoteRepository->getTagCloud();
        $template->minTag = $this->quoteRepository->getMinTag()[0]['mm'];
        $template->maxTag = $this->quoteRepository->getMaxTag()[0]['mx'];
    }

    /**
     * [LoginForm]
     * Validates the user's credentials.
     *
     * @param Form $form
     */
    public function processLoginForm(Form $form)
    {
        $values = $form->getValues();
        if ($values->remember) {
            $this->getUser()->setExpiration('14 days', false);
        } else {
            $this->getUser()->setExpiration('20 minutes', true);
        }

        try {
            $this->getUser()->login($values->username, $values->password);
            $this->flashMessage('Byl jsi úspěšně přihlášen!', 'success');
            if ($this->presenter->action == 'in') {
                $this->redirect('Homepage:default');
            }
            $this->redirect('this');
        } catch (Nette\Security\AuthenticationException $e) {
            $this->flashMessage($e->getMessage(), 'error');
        }
    }

    /**
     * For site search autocomplete.
     */
    public function handleSearchJson()
    {
        $this->sendJson($this->autocompleteService->getSearchBoxData());
    }

    /**
     * User logout signal.
     */
    public function handleLogout()
    {
        if ($this->user->isLoggedIn()) {
            $this->user->logout();
        }
        $this->flashMessage('Byl jsi odhlášen.', 'info');
        $this->redirect('Homepage:default');
    }

    /**
     * AddQuoteControl factory.
     *
     * @return \App\FrontModule\Components\AddQuote\AddQuoteControl
     */
    protected function createComponentAddQuoteControl()
    {
        return $this->addQuoteFactory->create();
    }

    /**
     * SearchForm factory.
     *
     * @return Form
     */
    protected function createComponentSearchForm()
    {
        $form = new Form();

        $form->onSubmit[] = function (Form $form) {
            $query = $form->getHttpData($form::DATA_TEXT, 'query');
            $this->redirect('Homepage:search', $query);
        };

        return $form;
    }

    /**
     * LoginForm factory
     *
     * @return LoginForm
     */
    protected function createComponentLoginForm()
    {
        $form = new LoginForm();
        $form->onSuccess[] = $this->processLoginForm;

        return $form;
    }

    /**
     * Add the HTML minification helper to the template.
     *
     * @param null $class
     * @return Nette\Application\UI\ITemplate
     */
    protected function createTemplate($class = NULL)
    {
        $template = parent::createTemplate($class);
        $template->registerHelper('minifyhtml', function ($in) {
            return \Minify_HTML::minify($in);
        });
        return $template;
    }
}