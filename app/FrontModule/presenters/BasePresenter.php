<?php

namespace App\FrontModule;

use Kollarovic\Thumbnail\AbstractGenerator;
use Nette,
    Model;
use WebLoader\Compiler;
use WebLoader\FileCollection;
use WebLoader\Filter\LessFilter;
use WebLoader\Nette\JavaScriptLoader;

/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends Nette\Application\UI\Presenter
{
    protected $thumbGenerator;

    protected function createComponentCss()
    {
        $files = new FileCollection(WWW_DIR . '/public/front/css');
        $files->addFiles(array(
            'bootstrap.css',
            'bootstrap-theme.css',
            'front.less'
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
            'bootstrap.js',
            'nette.ajax.js',
            'live-form-validation.js',
            'main.js'
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
