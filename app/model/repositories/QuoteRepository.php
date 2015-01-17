<?php

namespace App\Model\Repositories;

use App\Model\Entities\Quote;
use App\Model\Entities\Tag;
use Kdyby\Doctrine\EntityManager;
use Nette\Object;

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

    /**
     * Persists a new quote and assigns given tags to it.
     *
     * @param Quote $quote
     * @param array $tags
     */
    public function create(Quote $quote, array $tags)
    {
        $this->em->persist($quote);

        // collect existing tags
        $existing_tags = $this->em->getDao(Tag::getClassName())->findAll();
        $ex = array();

        /** @var Tag $e */
        foreach ($existing_tags as $e) {
            $ex[$e->getTag()] = $e;
        }

        // persist new tags, omitting the existing ones
        foreach ($tags as $tag) {
            if (array_key_exists($tag, $ex)) {
                $t = $ex[$tag];
            } else {
                $t = new Tag();
                $t->setTag($tag);
                $this->em->persist($t);
            }

            $t->assignToQuote($quote);
            $quote->addTag($t);
        }
    }

    /**
     * Get all quotations sorted by date descending.
     *
     * @param int $limit LIMIT statement part parameter
     * @return array
     */
    public function findAllByDateDesc($limit = 10)
    {
        return $this->quoteDao->findBy(['approved' => true], ['posted' => 'DESC'], $limit);
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