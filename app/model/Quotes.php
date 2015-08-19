<?php

namespace App\Model;

use App\Model\Repositories\QuoteRepository;

class Quotes
{
    /** @var QuoteRepository */
    private $quoteRepository;

    /**
     * @param QuoteRepository $quoteRepository
     */
    function __construct(QuoteRepository $quoteRepository)
    {
        $this->quoteRepository = $quoteRepository;
    }
}