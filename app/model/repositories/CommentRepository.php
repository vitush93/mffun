<?php

namespace App\Model\Repositories;


use App\Model\Entities\Comment;
use App\Model\Entities\Quote;
use App\Model\Entities\User;
use Doctrine\ORM\EntityNotFoundException;
use Kdyby\Doctrine\EntityManager;
use Nette\InvalidArgumentException;
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
     * Creates a new comment.
     *
     * @param $text
     * @param $qid
     * @param $user_id
     * @param int $parent_id
     * @return Comment
     * @throws EntityNotFoundException
     * @throws InvalidArgumentException
     */
    function create($text, $qid, $user_id, $parent_id = 0)
    {
        /** @var Quote $quote */
        $quote = $this->em->getRepository(Quote::class)->find($qid);
        if (!$quote) throw new EntityNotFoundException('Quote with id ' . $qid . ' not found.');

        /** @var User $user */
        $user = $this->em->getRepository(User::class)->find($user_id);
        if (!$user) throw new EntityNotFoundException('User with id ' . $user_id . ' not found.');

        if ($parent_id > 0) {

            /** @var Comment $parent */
            $parent = $this->em->getRepository(Comment::class)->find($parent_id);
            if (!$parent) throw new EntityNotFoundException('Parent comment with id ' . $parent_id . ' not found.');

            if ($parent->getParent() > 0) {
                throw new InvalidArgumentException('Comment with id ' . $parent_id . ' is not a top-level comment.');
            }
        }

        $comment = new Comment();
        $comment->setText($text);
        $comment->setQuote($quote);
        $comment->setUser($user);
        $comment->setParent($parent_id);

        $this->em->persist($comment);

        return $comment;
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
     * @param $limit
     * @param $offset
     * @param int $perThread number of responses per thread to get.
     * @return
     */
    function quoteComments($qid, $limit = null, $offset = null, $perThread = null)
    {
        $q = $this->em->getRepository(Quote::class)->find($qid);

        $comments = $this->em->getRepository(Comment::class)->findBy([
            'quote' => $q,
            'parent' => 0
        ], ['posted' => 'DESC'], $limit, $offset);

        // top level comment of parent = 0
        $arr[0] = $comments;

        /** @var Comment $c */
        foreach ($comments as $c) {

            $parent_id = $c->getId();

            /** @var Comment $child */
            foreach ($this->thread($parent_id, $perThread) as $child) {

                // insert child comment with parent = $parent_id to appropriate 'box'
                $arr[$parent_id][] = $child;
            }
        }

        return $arr;
    }
}