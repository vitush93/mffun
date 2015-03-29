<?php

namespace App\FrontModule\Presenters;

use App\FrontModule\Components\AddQuote\IAddQuoteControlFactory;
use App\FrontModule\Forms\LoginForm;
use App\Model\Repositories\QuoteRepository;
use Kdyby\Doctrine\EntityManager;
use Kollarovic\Thumbnail\AbstractGenerator;
use Model;
use Nette;
use Nette\Application\UI\Form;

/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends Nette\Application\UI\Presenter
{
    /** @var AbstractGenerator @inject */
    public $thumbGenerator;

    /** @var IAddQuoteControlFactory @inject */
    public $addQuoteFactory;

    /** @var QuoteRepository @inject */
    public $quoteRepository;

    /** @var EntityManager @inject */
    public $em;

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

    public function beforeRender()
    {
        $string = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc hendrerit feugiat tortor in ultricies. Sed aliquet enim non mauris consectetur, eget dapibus turpis gravida. Praesent viverra mauris vel arcu tristique gravida. Etiam imperdiet fringilla consectetur.';
        $j = 0;
        for ($i = 0; $i < strlen($string); $i++) {
            if (!in_array($string[$i], array(',', '.'))) {
                $string[$j] = $string[$i];
                $j++;
            }
        }
        $this->template->tags = explode(' ', $string);
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
            $this->redirect('Homepage:default');
        } catch (Nette\Security\AuthenticationException $e) {
            $this->flashMessage($e->getMessage(), 'error');
        }
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
        $template->registerHelper('thumbnail', $this->thumbGenerator->thumbnail);
        $template->registerHelper('minifyhtml', function ($in) {
            return \Minify_HTML::minify($in);
        });
        return $template;
    }
}
