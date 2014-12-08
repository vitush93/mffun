<?php

namespace App\Model\Repositories;

use Nette\NotImplementedException;
use Nette\Object;
use Kdyby\Doctrine\EntityManager;
use App\Model\Entities\Quote;

class QuoteRepository extends Object
{

    /** @var EntityManager */
    private $em;

    /** @var \Kdyby\Doctrine\EntityDao */
    private $quoteDao;

    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
        $this->quoteDao = $entityManager->getDao(Quote::getClassName());
    }

    public function create(Quote $quote, array $tags) {
        throw new NotImplementedException();
    }

    /**
     * Get all quotations sorted by date descending.
     *
     * @param int $limit LIMIT statement part parameter
     * @return array
     */
    public function findAllByDateDesc($limit = 10)
    {
        return $this->quoteDao->findBy([], ['posted' => 'DESC'], $limit);
    }

    /**
     * Get all quotes sorted by rating descending.
     *
     * @param int $limit
     * @return array
     */
    public function findAllByRatingDesc($limit = 10)
    {
        return $this->quoteDao->findBy([], ['rating' => 'DESC'], $limit);
    }
}