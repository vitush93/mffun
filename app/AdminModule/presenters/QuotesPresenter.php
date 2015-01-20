<?php
/**
 * Created by PhpStorm.
 * User: Vít
 * Date: 1/20/2015
 * Time: 8:28 PM
 */

namespace App\AdminModule\Presenters;

use App\Model\Entities\Quote;
use App\Model\Repositories\QuoteRepository;
use Nette\Application\BadRequestException;

class QuotesPresenter extends BasePresenter
{
    /** @var QuoteRepository @inject */
    public $quoteRepository;

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

        $this->quoteRepository->deny($quote);
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

        $this->quoteRepository->approve($quote);
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