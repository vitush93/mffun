<?php

namespace App\Model\Repositories;


use App\Model\Entities\Comment;
use App\Model\Entities\Quote;
use Kdyby\Doctrine\EntityManager;
use Nette\Object;

class CommentRepository extends Object
{
    /** @var EntityManager */
    private $em;

    /**
     * @param EntityManager $entityManager
     */
    function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

    /**
     * @param int $cid parent Comment id.
     * @param int $limit
     * @param int $offset
     * @return array
     */
    function thread($cid, $limit = 3, $offset = 0)
    {
        $comments = $this->em->getRepository(Comment::class)
            ->createQueryBuilder('c')
            ->where('c.parent = :cid')
            ->orderBy('c.posted', 'ASC')
            ->setParameter('cid', $cid)
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()->getResult();

        return $comments;
    }

    /**
     * @param int $qid Quote id.
     * @param int $threads number if threads to get.
     * @param int $perThread number of responses per thread to get.
     */
    function quoteComments($qid, $threads = 10, $perThread = 3)
    {
        $q = $this->em->getRepository(Quote::class)->find($qid);

        $comments = $this->em->getRepository(Comment::class)->findBy([
            'quote' => $q,
            'parent' => 0
        ], ['posted' => 'DESC'], $threads);

        // top level comment of parent = 0
        $arr[0] = $comments;

        /** @var Comment $c */
        foreach ($comments as $c) {

            $parent_id = $c->getId();

            /** @var Comment $child */
            foreach ($this->thread($parent_id) as $child) {

                // insert child comment with parent = $parent_id to appropriate 'box'
                $arr[$parent_id][] = $child;
            }
        }

        return $arr;
    }
}