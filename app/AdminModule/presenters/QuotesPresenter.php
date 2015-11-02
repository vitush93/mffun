<?php

namespace App\AdminModule\Presenters;

use App\Libs\BootstrapForm;
use App\Model\Entities\Quote;
use App\Model\Entities\User;
use App\Model\Repositories\QuoteRepository;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Form;
use Nette\Mail\IMailer;
use Nette\Utils\ArrayHash;

class QuotesPresenter extends BasePresenter
{
    /** @var QuoteRepository @inject */
    public $quoteRepository;

    /** @var IMailer @inject */
    public $mailer;

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

    public function handleEdit($qid)
    {
        $quote = $this->quoteRepository->find($qid);
        if (!$quote) throw new BadRequestException;

        $form = $this['editForm'];
        $form['text']->setDefaultValue($quote->getText());
        $form['qid']->setDefaultValue($qid);

        if ($this->isAjax()) {
            $this->redrawControl();
        } else {
            $this->redirect('default');
        }
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

        /** @var User $admin */
        $admin = $this->em->find(User::class, $this->user->id);

        if ($quote->getUserEmail()) {
            // send e-mail
            $recipient = $quote->getUserEmail();
            $subject = 'MFFun - Vaše citace byla zamítnuta';
            $message = '
        Vaše citace byla zamítnuta administrátorem ' . $admin->getUsername() . '. Následuje text citace:<br><br>
        ' . $quote->getText() . '
        ';
            $this->sendMail($recipient, $subject, $message);
        }

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

        /** @var User $admin */
        $admin = $this->em->find(User::class, $this->user->id);

        if ($quote->getUserEmail()) {
            // send e-mail
            $recipient = $quote->getUserEmail();
            $subject = 'MFFun - Vaše citace byla schválena';
            $message = '
        Vaše citace byla schválena administrátorem ' . $admin->getUsername() . '. Následuje text citace:<br><br>
        ' . $quote->getText() . '
        ';
            $this->sendMail($recipient, $subject, $message);
        }

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
    protected function createComponentEditForm()
    {
        $form = new Form();

        $form->addTextArea('text', 'Text')
            ->setRequired()
            ->getControlPrototype()->style = "height: 300px";
        $form->addHidden('qid', '');

        $form->addSubmit('process', 'Uložit');
        $form->onSuccess[] = function (Form $form, ArrayHash $values) {
            $q = $this->quoteRepository->find($values->qid);
            $q->setText($values->text);

            $this->em->flush();
            $this->redirect('this');
        };

        return BootstrapForm::makeBootstrap($form);
    }
}