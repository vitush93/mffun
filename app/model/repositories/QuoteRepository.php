<?php

namespace App\Model\Repositories;

use App\Model\Entities\Comment;
use App\Model\Entities\Quote;
use App\Model\Entities\Tag;
use App\Model\Entities\User;
use Doctrine\Common\Collections\Criteria;
use Kdyby\Doctrine\EntityManager;
use Nette\Object;

class QuoteRepository extends Object
{

    /** @var EntityManager */
    private $em;

    /** @var \Kdyby\Doctrine\EntityDao */
    private $quoteDao;

    private $commentDao;

    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
        $this->quoteDao = $entityManager->getDao(Quote::getClassName());
        $this->commentDao = $entityManager->getDao(Comment::getClassName());
    }

    public function findTopLevelComments($qid)
    {
        return $this->find($qid)->getComments()->matching(
            Criteria::create()
                ->where(Criteria::expr()->eq('parent', 0))
                ->orderBy(['posted' => 'ASC'])
        );
    }

    /**
     * @param $id
     * @return null|Quote
     */
    public function find($id)
    {
        return $this->quoteDao->find($id);
    }

    /**
     * @param string $text comment text
     * @param int $qid quote id
     * @param int $uid user id
     * @param int $parentid parent comment
     */
    public function postComment($text, $qid, $uid, $parentid = null)
    {
        $clean = new \HTMLPurifier();
        $text = $clean->purify($text);
        if (strlen($text) == 0) return;

        $quote = $this->find($qid);
        $user = $this->em->find(User::getClassName(), $uid);

        $comment = new Comment();
        $comment->setUser($user);
        $comment->setText($text);
        $comment->setQuote($quote);

        if ($parentid) {
            $comment->setParent($parentid);
        } else {
            $comment->setParent(0);
        }

        $this->em->persist($comment);
    }

    /**
     * @return array
     */
    public function findAllDenied()
    {
        return $this->quoteDao->findBy(['status' => Quote::STATUS_DENIED]);
    }

    /**
     * @return array
     */
    public function findAll()
    {
        return $this->quoteDao->findAll();
    }

    /**
     * Approve the quote and decrease authored user's c-rank.
     *
     * @param Quote $quote
     */
    public function deny(Quote $quote)
    {
        $quote->deny();
        $quote->getUser()->decreaseCrank();
    }

    /**
     * Approve the quote and increase authored user's c-rank.
     *
     * @param Quote $quote
     */
    public function approve(Quote $quote)
    {
        $quote->approve();
        $quote->getUser()->increaseCrank();
    }

    /**
     * Remove the quote by id and flush.
     *
     * @param $id
     */
    public function delete($id)
    {
        $quote = $this->quoteDao->find($id);

        if ($quote != null) $this->quoteDao->delete($quote);
    }

    /**
     * Find all yet unapproved quotations.
     *
     * @return array
     */
    public function findAllUnapproved()
    {
        return $this->quoteDao->findBy(['status' => Quote::STATUS_NEED_APPROVAL]);
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
        return $this->quoteDao->findBy(['status' => Quote::STATUS_APPROVED], ['approved' => 'DESC'], $limit);
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