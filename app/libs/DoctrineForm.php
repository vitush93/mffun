<?php

namespace App\Libs;

use Kdyby\Doctrine\Entities\BaseEntity;
use Nette\Application\UI\Form;
use Nette\Forms\Container;
use Nette\Forms\IControl;

class DoctrineForm extends Form
{
    private $entity;

    /**
     * Only for very simple forms
     *
     * @param BaseEntity $entity
     * @return self
     */
    public function bindEntity(BaseEntity $entity)
    {
        $this->entity = $entity;

        foreach ($this->getComponents() as $name => $control) {
            if ($control instanceof IControl) {
                $method = 'get' . ucfirst($name);
                if (method_exists($this->entity, $method)) {
                    if (!$this || !$this->isAnchored() || !$this->isSubmitted()) {
                        $control->setValue($entity->$method());
                    }
                }
            } elseif ($control instanceof Container) {
                // TODO
            }
        }

        return $this;
    }

    /**
     * @return self
     */
    public function reset()
    {
        $this->setDefaults(array());
        return $this;
    }
}