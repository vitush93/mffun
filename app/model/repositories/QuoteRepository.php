<?php

namespace App\Model\Repositories;

use Nette\Object;
use Kdyby\Doctrine\EntityManager;
use App\Model\Entities\Quote;

class QuoteRepository extends Object implements IRepository
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
     * @param array|object|\Traversable $quote
     * @return array|object|\Traversable|void
     */
    public function add($quote)
    {
        $this->quoteDao->save($quote);
    }

    /**
     * @param integer $id
     * @return mixed
     */
    public function find($id)
    {
        // TODO: Implement find() method.
    }

    /**
     * @return array
     */
    public function findAll()
    {
        // TODO: Implement findAll() method.
    }

    /**
     * @param integer $id
     * @return void
     */
    public function remove($id)
    {
        // TODO: Implement remove() method.
    }


    public function getAllByDate()
	{
		// TODO
	}

	public function getAllByRatingDesc()
	{
		// TODO
	}

	public function getAllByCommentsDesc()
	{
		// TODO
	}
}