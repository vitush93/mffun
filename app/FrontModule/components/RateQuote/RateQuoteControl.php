<?php

namespace App\FrontModule\Components;

use App\Model\Entities\Quote;
use App\Model\Entities\QuoteRating;
use App\Model\Entities\User;
use App\Model\Services\RatingService;
use Kdyby\Doctrine\EntityManager;
use Nette\Application\UI\Control;

interface IRateQuoteControlFactory
{
    /** @return RateQuoteControl */
    function create();
}

class RateQuoteControl extends Control
{
    /** @var RatingService */
    private $ratingService;

    /** @var EntityManager */
    private $em;

    /**
     * @param EntityManager $entityManager
     * @param RatingService $ratingService
     */
    public function __construct(EntityManager $entityManager, RatingService $ratingService)
    {
        parent::__construct();

        $this->em = $entityManager;
        $this->ratingService = $ratingService;
    }

    /**
     * @param $qid Quote id.
     */
    public function handleUp($qid)
    {
        if (!$this->presenter->user->isLoggedIn()) {
            $this->presenter->sendJson([]);
        } else {
            $response = $this->ratingService->rateQuoteUp($qid, $this->presenter->user->id);

            $this->presenter->sendResponse($response);
        }
    }

    /**
     * @param $qid Quote id.
     */
    public function handleDown($qid)
    {
        if (!$this->presenter->user->isLoggedIn()) {
            $this->presenter->sendJson([]);
        } else {
            $response = $this->ratingService->rateQuoteDown($qid, $this->presenter->user->id);

            $this->presenter->sendResponse($response);
        }
    }

    public function render($q)
    {
        $this->template->q = $q['q'];
        $this->template->up = null;
        $this->template->down = null;

        if ($this->presenter->user->isLoggedIn()) {
            $uid = $this->presenter->user->id;
            $user = $this->em->find(User::class, $uid);
            $quote = $this->em->find(Quote::class, $q['q']);
            /** @var QuoteRating|null $r */
            $r = $this->em->getRepository(QuoteRating::class)->findOneBy(['quote' => $quote, 'user' => $user]);
            if ($r) {
                $r->isPositive() ? $this->template->up = true : $this->template->down = true;
            }
        }

        $this->template->setFile(__DIR__ . '/RateQuote.latte');
        $this->template->render();
    }
}