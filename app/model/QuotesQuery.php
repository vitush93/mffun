<?php

namespace App\Model;

use Doctrine\ORM\EntityManager;

class QuotesQuery
{
    /** @var EntityManager */
    private $em;

    /** @var int */
    private $page;

    /**
     * @param EntityManager $entityManager
     * @internal param QuoteRepository $quoteRepository
     */
    function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

    function createBaseQuery()
    {
        return $this->em->createQueryBuilder();
    }

    function teacher($teacher)
    {

    }

    function tag($tag)
    {

    }

    function search($query)
    {

    }

    function setPage($page)
    {
        $this->page = $page;
    }
}