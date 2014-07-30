<?php

namespace App\Model\Repositories;

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