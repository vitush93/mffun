<?php

namespace App\AdminModule;

use App\Libs\BootstrapForm;
use Kdyby\Doctrine\Entities\BaseEntity;

class ModalFormControl extends \Nette\Application\UI\Control
{
    private $form;
    private $formName;

    public function __construct(\Nette\Application\UI\Form $form, $formName)
    {
        parent::__construct();
        $this->form = $form;
        $this->formName = $formName;
    }

    /**
     * Změní název formuláře (vč. jeho ID, které je generováno automaticky)
     *
     * @param $formName
     *
     * @return $this;
     */
    public function setFormName($formName)
    {
        $this->formName = $formName;

        return $this;
    }

    /**
     * @param $name
     *
     * @return mixed
     */
    public function getInput($name)
    {
        return $this['form'][$name];
    }

    /**
     * Změní formulář k vykreslení
     *
     * @param \Nette\Application\UI\Form $form
     *
     * @return $this
     */
    public function setForm(\Nette\Application\UI\Form $form)
    {
        $this->form = $form;

        return $this;
    }

    /**
     * Nastaví výchozí hodnoty pro formulář
     *
     * @param $defaults \Nette\Database\Table\ActiveRow or array
     *
     * @throws \Nette\InvalidArgumentException
     */
    public function setDefaults($defaults)
    {
        if ($defaults instanceof \Nette\Database\Table\ActiveRow) {
            $this['form']->setDefaults($defaults->toArray());
        } elseif (is_array($defaults)) {
            $this['form']->setDefaults($defaults);
        } elseif ($defaults instanceof BaseEntity) {
            
        } else {
            throw new \Nette\InvalidArgumentException('Passed argument is not array nor ActiveRow');
        }

        return $this;
    }

    /**
     * Překreslí modal okno - redrawControl()
     *
     * @return $this
     * @throws \Exception
     */
    public function refresh()
    {
        if ($this->presenter->isAjax()) {
            $this->redrawControl();
        } else {
            throw new \Exception('class="ajax" is probably missing');
        }

        return $this;
    }

    /**
     * Zobrazí modal okno ajaxem a překreslí ho - redrawControl()
     */
    public function show()
    {
        $this->template->show = true;

        return $this;
    }

    /**
     * Vymaže hodnoty ve formuláři ajaxem a překreslí ho - redrawControl()
     *
     * @return ModalFormControl
     */
    public function reset()
    {
        $this['form']->setValues(array(), true);

        return $this;
    }

    public function render()
    {
        $this->template->setFile(__DIR__ . '/ModalForm.latte');
        $this->template->formId = \Nette\Utils\Strings::webalize($this->formName);
        $this->template->formName = $this->formName;
        $this->template->render();
    }

    protected function createComponentForm()
    {
        $this->form->addButton('close', 'Zrušit')
            ->getControlPrototype()
            ->addAttributes(array('data-dismiss' => 'modal'));
        $this->form['close']->setOmitted();

        return BootstrapForm::makeBootstrap($this->form);
    }
}