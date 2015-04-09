<?php

namespace App\AdminModule\Presenters;

use App\Model\Entities\Quote;
use App\Model\Repositories\QuoteRepository;
use Kdyby\Doctrine\EntityManager;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Presenter;

class QuotesPresenter extends Presenter
{
    /** @var QuoteRepository @inject */
    public $quoteRepository;

    /** @var EntityManager @inject */
    public $em;

    use BasePresenterTrait;

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
     * @param $id
     * @throws BadRequestException
     */
    public function handleDeny($id)
    {
        $quote = $this->quoteRepository->find($id);

        if (!$this->checkQuote($quote)) throw new BadRequestException();

        $quote->deny();
        $this->em->flush();

        $this->flashMessage('Citace byla odmítnuta', 'info');
        $this->redirect('this');
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
     * @param $id
     * @throws BadRequestException
     */
    public function handleApprove($id)
    {
        $quote = $this->quoteRepository->find($id);

        if (!$this->checkQuote($quote)) throw new BadRequestException();

        $quote->approve();
        $this->em->flush();

        $this->flashMessage('Citace byla schválena', 'success');
        $this->redirect('this');
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
}