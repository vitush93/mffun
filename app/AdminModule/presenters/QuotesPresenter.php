<?php

namespace App\AdminModule\Presenters;

use App\FrontModule\Components\AddQuote\IAddQuoteControlFactory;
use App\Libs\BootstrapForm;
use App\Model\Entities\Quote;
use App\Model\Repositories\QuoteRepository;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Form;

class QuotesPresenter extends BasePresenter
{
    /** @var QuoteRepository @inject */
    public $quoteRepository;

    /** @var IAddQuoteControlFactory @inject */
    public $addQuoteControlFactory;

    public function renderDefault()
    {
        $this->template->quotes = $this->quoteRepository->findAllUnapproved();
    }

    public function renderDenied()
    {
        $this->setView('default');
        $this->template->quotes = $this->quoteRepository->findAllDenied();
    }

    public function renderAll()
    {
        $this->setView('default');
        $this->template->quotes = $this->quoteRepository->findAll();
    }

    /**
     * Deny quote with given id.
     *
     * @param $qid
     * @throws BadRequestException
     */
    public function handleDeny($qid)
    {
        $quote = $this->quoteRepository->find($qid);

        if (!$this->checkQuote($quote)) throw new BadRequestException();

        $quote->deny();
        $this->em->flush();

        if ($this->isAjax()) {
            if ($this->action == 'default') {
                $this->sendJson(['hide' => true]);
            } else {
                $this->setView('approve');
                $this->template->q = $quote;
            }
        } else {
            $this->flashMessage('Citace byla odmítnuta', 'info');
            $this->redirect('this');
        }
    }

    /**
     * @param $object
     * @return bool
     */
    private function checkQuote($object)
    {
        if ($object != null && $object instanceof Quote) return true;
        else return false;
    }

    /**
     * Approve quote with given id.
     *
     * @param $qid
     * @throws BadRequestException
     */
    public function handleApprove($qid)
    {
        $quote = $this->quoteRepository->find($qid);

        if (!$this->checkQuote($quote)) throw new BadRequestException();

        $quote->approve();
        $this->em->flush();

        if ($this->isAjax()) {
            if ($this->action == 'default' || $this->action == 'denied') {
                $this->sendJson(['hide' => true]);
            } else {
                $this->setView('deny');
                $this->template->q = $quote;
            }
        } else {
            $this->flashMessage('Citace byla schválena', 'info');
            $this->redirect('this');
        }
    }

    /**
     * Delete quote by given id.
     *
     * @param $id
     */
    public function handleDelete($id)
    {
        $this->quoteRepository->delete($id);
        $this->flashMessage("Položka byla smazána.", "info");
        $this->redirect('this');
    }

    /**
     * @return Form
     */
    protected function createComponentAddQuote()
    {
        $control = $this->addQuoteControlFactory->create();
        $closure = function () use ($control) {
            $form = $control->getForm();
            BootstrapForm::makeBootstrap($form);
        };
        $control->modifyForm($closure);

        return $control;
    }
}