<?php

namespace App\FrontModule;

use App\FrontModule\Forms\LoginForm;
use Kollarovic\Thumbnail\AbstractGenerator;
use Nette,
    Model;
use WebLoader\Compiler;
use WebLoader\FileCollection;
use WebLoader\Filter\LessFilter;
use WebLoader\Nette\JavaScriptLoader;
use Nette\Application\UI\Form;
use Nette\Application\Application;
use App\FrontModule\Components\AddQuote\IAddQuoteControlFactory;

/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends Nette\Application\UI\Presenter
{
    protected $thumbGenerator;

    /** @var IAddQuoteControlFactory @inject */
    public  $addQuoteFactory;

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

    protected function createComponentAddQuoteControl()
    {
        return $this->addQuoteFactory->create();
    }

    protected function createComponentLoginForm()
    {
        $form = new LoginForm();
        $form->onSuccess[] = $this->processLoginForm;

        return $form;
    }

    protected function createComponentCss()
    {
        $files = new FileCollection(WWW_DIR . '/public/front/css');
        $files->addFiles(array(
            'bootstrap.css',
            'reset.css',
            'style.less',
            'font-awesome.css',
        ));

        $compiler = Compiler::createCssCompiler($files, WWW_DIR . '/temp');

        $compiler->addFileFilter(new LessFilter());

        $compiler->addFilter(function ($code) {
            $cssmin = new \CSSmin();
            return $cssmin->run($code);
        });

        $compiler->addFilter(function ($code) {
            return \Minify_CSS_UriRewriter::rewrite($code, 'public/front/css');
        });

        $control = new \WebLoader\Nette\CssLoader($compiler, $this->template->basePath . '/temp');
        $control->setMedia('screen');

        return $control;
    }

    protected function createComponentJs()
    {
        $files = new FileCollection(WWW_DIR . '/public/front/js');
        $files->addFiles(array(
            'jquery.js',
            'TweenMax.min.js',
            'jquery.gsap.min.js',
            'bootstrap.js',
            'nette.ajax.js',
            'live-form-validation.js',
            'script.js'
        ));

        $compiler = Compiler::createJsCompiler($files, WWW_DIR . '/temp');

        $compiler->addFilter(function ($code) {
            return \JSMin::minify($code);
        });

        return new JavaScriptLoader($compiler, $this->template->basePath . '/temp');
    }

    public function injectThumbGenerator(AbstractGenerator $abstractGenerator)
    {
        $this->thumbGenerator = $abstractGenerator;
    }

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
