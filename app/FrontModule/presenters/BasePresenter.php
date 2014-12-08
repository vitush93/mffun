<?php

namespace App\FrontModule\Presenters;

use App\FrontModule\Forms\LoginForm;
use Kollarovic\Thumbnail\AbstractGenerator;
use Nette,
    Model;
use Nette\Application\UI\Form;
use App\FrontModule\Components\AddQuote\IAddQuoteControlFactory;

/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends Nette\Application\UI\Presenter
{
    /** @var AbstractGenerator @inject  */
    public  $thumbGenerator;

    /** @var IAddQuoteControlFactory @inject */
    public  $addQuoteFactory;

    /**
     * User logout signal.
     */
    public function handleLogout()
    {
        if ($this->user->isLoggedIn()) {
            $this->user->logout();
        }
        $this->flashMessage('Byl jsi odhlášen.', 'info');
        $this->redirect('default');
    }

    public function beforeRender()
    {
        $string = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc hendrerit feugiat tortor in ultricies. Sed aliquet enim non mauris consectetur, eget dapibus turpis gravida. Praesent viverra mauris vel arcu tristique gravida. Etiam imperdiet fringilla consectetur. Phasellus cursus ante eget fermentum dignissim. Vestibulum et neque nec tellus lobortis luctus quis vitae lacus. Nullam semper placerat nunc, ut imperdiet mi faucibus id. Ut sit amet magna eget justo feugiat scelerisque ac pretium augue.

Phasellus id neque massa. Aenean venenatis magna a purus laoreet lobortis. Phasellus pulvinar nulla a lorem rutrum porta ut nec lacus. Duis magna neque, cursus eu malesuada a, ornare sit amet lacus. Morbi libero lectus, pretium quis lectus non, elementum elementum urna. Aliquam eu accumsan tellus. Praesent convallis arcu id turpis ultrices pellentesque. In laoreet ut neque quis condimentum.

In leo arcu, aliquam non magna non, vestibulum luctus lacus. Proin placerat, risus a laoreet facilisis, elit erat auctor metus, vitae auctor nulla turpis ac felis. Integer volutpat adipiscing sagittis. Nulla posuere neque ut tellus malesuada lacinia. Aliquam dictum ac tortor a blandit. Nulla accumsan sagittis lectus eu convallis. Aliquam mattis placerat nisl, at fermentum risus suscipit eget. Praesent purus nibh, porta eget enim vitae, vestibulum mollis arcu. Aliquam eget tempus nunc. Mauris vel metus congue, vulputate nunc et, viverra velit. Nam semper convallis facilisis';
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
