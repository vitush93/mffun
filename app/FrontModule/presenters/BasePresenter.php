<?php

namespace App\FrontModule\Presenters;

use App\FrontModule\Components\AddQuote\IAddQuoteControlFactory;
use App\FrontModule\Forms\LoginForm;
use App\Model\Repositories\QuoteRepository;
use Doctrine\ORM\Query;
use Kdyby\Doctrine\EntityManager;
use Model;
use Nette;
use Nette\Application\UI\Form;
use Nette\Application\UI\Presenter;

class BasePresenter extends Presenter
{
    /** @var IAddQuoteControlFactory @inject */
    public $addQuoteFactory;

    /** @var QuoteRepository @inject */
    public $quoteRepository;

    /** @var EntityManager @inject */
    public $em;

    public function beforeRender()
    {
        if ($this->isAjax()) return; // skip for ajax requests

        $template = $this->template;
        $template->tags = $this->quoteRepository->getTagCloud();
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
            $this->redirect('this');
        } catch (Nette\Security\AuthenticationException $e) {
            $this->flashMessage($e->getMessage(), 'error');
        }
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
     * LoginForm factory
     *
     * @return LoginForm
     */
    protected function createComponentLoginForm()
    {
        $form = new LoginForm();
        $form->onSuccess[] = $this->processLoginForm;
        $form->addProtection();

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