<?php

namespace App\Model\Services;


use App\Model\Entities\Comment;
use App\Model\Entities\Quote;
use App\Model\Repositories\QuoteRepository;
use Doctrine\ORM\Query;

class QuoteRestService extends RestService
{
    const QUOTES_LATEST = 'latest';
    const QUOTES_TOP = 'top';
    const QUOTES_MOST_COMMENTED = 'mostcommented';

    /** @var QuoteRepository */
    private $quoteRepository;

    /**
     * @param QuoteRepository $quoteRepository
     */
    function __construct(QuoteRepository $quoteRepository)
    {
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * @param $results
     * @param Query $query
     */
    private function appendComments(& $results, Query $query)
    {
        $i = 0;

        /** @var Quote $q */
        foreach ($query->getResult() as $q) {
            $results[$i]['comment_count'] = $q->getComments()->count();
            $results[$i]['top_comments'] = array();

            /** @var Comment $c */
            foreach ($q->getBestComments() as $c) {
                $comment['avatar'] = $c->getUser()->getAvatar();
                $comment['text'] = $c->getText();
                $comment['up'] = $c->getRatingUp();
                $comment['down'] = $c->getRatingDown();

                $results[$i]['top_comments'][] = $comment;
            }

            $i++;
        }
    }

    /**
     * @param int $limit
     * @param int $offset
     *
     * @return \Nette\Application\Responses\JsonResponse
     */
    function latest($limit, $offset)
    {
        $quotesQuery = $this->quoteRepository->allApprovedQuery($limit, $offset, self::QUOTES_LATEST);
        $quotes = $quotesQuery->getArrayResult();

        $this->appendComments($quotes, $quotesQuery);

        return $this->createJsonResponse($quotes);
    }

    /**
     * @param $limit
     * @param $offset
     *
     * @return \Nette\Application\Responses\JsonResponse
     */
    function top($limit, $offset)
    {
        $quotesQuery = $this->quoteRepository->allApprovedQuery($limit, $offset, self::QUOTES_TOP);
        $quotes = $quotesQuery->getArrayResult();

        $this->appendComments($quotes, $quotesQuery);

        return $this->createJsonResponse($quotes);
    }

    /**
     * @param $limit
     * @param $offset
     *
     * @return \Nette\Application\Responses\JsonResponse
     */
    function mostCommented($limit, $offset)
    {
        $quotesQuery = $this->quoteRepository->allApprovedQuery($limit, $offset, self::QUOTES_MOST_COMMENTED);
        $quotes = $quotesQuery->getArrayResult();

        $this->appendComments($quotes, $quotesQuery);

        return $this->createJsonResponse($quotes);
    }
}