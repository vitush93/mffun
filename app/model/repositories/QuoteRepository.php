<?php

namespace App\Model\Repositories;

use App\Model\Entities\Comment;
use App\Model\Entities\Quote;
use App\Model\Entities\Subject;
use App\Model\Entities\Tag;
use App\Model\Entities\Teacher;
use App\Model\Entities\User;
use Doctrine\ORM\Query;
use Kdyby\Doctrine\EntityManager;
use Nette\Object;

class QuoteRepository extends Object
{

    /** @var EntityManager */
    private $em;

    /** @var \Kdyby\Doctrine\EntityDao */
    private $quoteDao;

    /** @var \Kdyby\Doctrine\EntityDao */
    private $commentDao;

    private $order = [
        'latest' => 'order by q.approved DESC',
        'top' => 'order by q.rating DESC, q.approved DESC',
        'mostcommented' => 'order by c DESC, q.approved DESC'
    ];

    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
        $this->quoteDao = $entityManager->getDao(Quote::getClassName());
        $this->commentDao = $entityManager->getDao(Comment::getClassName());
    }

    public function getMinTag()
    {
        return $this->em->createQueryBuilder()
            ->select('min(size(t.quotations)) as mm')
            ->from('App\Model\Entities\Tag', 't')
            ->getQuery()->getScalarResult();
    }

    public function getMaxTag()
    {
        return $this->em->createQueryBuilder()
            ->select('max(size(t.quotations)) as mx')
            ->from('App\Model\Entities\Tag', 't')
            ->getQuery()->getScalarResult();
    }

    /**
     * @return array
     */
    public function getTagCloud()
    {
        $q = $this->em->createQuery('
        select t, size(t.quotations) as density
        from App\Model\Entities\Tag t
        where size(t.quotations) > 0
        ');


        return $q->getResult();
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

        $comment = new Comment();
        $comment->setUser($this->em->getReference(User::class, $uid));
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
     * @param $id
     * @return null|Quote
     */
    public function find($id)
    {
        return $this->quoteDao->find($id);
    }

    /**
     * Returns array of Teacher object ordered by name.
     *
     * @return array
     */
    public function findTeachers()
    {
        return $this->em->getRepository(Teacher::class)->findBy([], ['name' => 'ASC']);
    }

    /**
     * Returns array of Subject object ordered by name.
     *
     * @return array
     */
    public function findSubjects()
    {
        return $this->em->getRepository(Subject::class)->findBy([], ['name' => 'ASC']);
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
                $t = new Tag($tag);
                $this->em->persist($t);
            }

            $t->assignToQuote($quote);
        }
    }

    public function search($query)
    {
        return $this->em->createQueryBuilder()
            ->select('q,t,s')
            ->from('App\Model\Entities\Quote', 'q')
            ->leftJoin('q.teacher', 't')
            ->leftJoin('q.subject', 's')
            ->where('q.status = :status')
            ->andWhere('q.text LIKE :query')
            ->groupBy('q.id')
            ->setMaxResults(50)
            ->setParameter('status', Quote::STATUS_APPROVED)
            ->setParameter('query', "%{$query}%")
            ->getQuery()->getResult();
    }

    public function findAllBySubject($subject, $limit, $offset)
    {
        return $this->em->createQueryBuilder()
            ->select('q,t,s')
            ->from('App\Model\Entities\Quote', 'q')
            ->leftJoin('q.teacher', 't')
            ->leftJoin('q.subject', 's')
            ->where('s.id = :subject')
            ->andWhere('q.status = :status')
            ->groupBy('q.id')
            ->orderBy('q.approved', 'DESC')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->setParameter('subject', $subject)
            ->setParameter('status', Quote::STATUS_APPROVED)
            ->getQuery()->getResult();
    }

    public function findAllByTeacher($teacher, $limit, $offset)
    {
        return $this->em->createQueryBuilder()
            ->select('q,t,s')
            ->from('App\Model\Entities\Quote', 'q')
            ->leftJoin('q.teacher', 't')
            ->leftJoin('q.subject', 's')
            ->where('t.id = :teacher')
            ->andWhere('q.status = :status')
            ->groupBy('q.id')
            ->orderBy('q.approved', 'DESC')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->setParameter('teacher', $teacher)
            ->setParameter('status', Quote::STATUS_APPROVED)
            ->getQuery()->getResult();
    }

    public function findAllByTag($tag, $limit, $offset)
    {
        return $this->em->createQueryBuilder()
            ->select('q,t,s')
            ->from('App\Model\Entities\Quote', 'q')
            ->leftJoin('q.tags', 'tg')
            ->leftJoin('q.teacher', 't')
            ->leftJoin('q.subject', 's')
            ->where("tg.id = :tag")
            ->andWhere('q.status = :status')
            ->groupBy('q.id')
            ->orderBy('q.approved', 'DESC')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->setParameter('tag', $tag)
            ->setParameter('status', Quote::STATUS_APPROVED)
            ->getQuery()->getResult();
    }

    public function getRandomQuotes($limit)
    {
        $ids = $this->em->createQueryBuilder()
            ->select('q.id')
            ->from('App\Model\Entities\Quote', 'q')
            ->where('q.status = :status')
            ->setParameter('status', Quote::STATUS_APPROVED)
            ->getQuery()
            ->getResult(Query::HYDRATE_ARRAY);
        $ids = array_map('current', $ids);
        $ids = array_rand($ids, $limit);

        return $this->em->createQueryBuilder()
            ->select('q,t,s')
            ->from('App\Model\Entities\Quote', 'q')
            ->leftJoin('q.tags', 'tg')
            ->leftJoin('q.teacher', 't')
            ->leftJoin('q.subject', 's')
            ->where('q.id IN (:ids)')
            ->andWhere('q.status = :status')
            ->groupBy('q.id')
            ->setParameter('status', Quote::STATUS_APPROVED)
            ->setParameter('ids', $ids)
            ->getQuery()->getResult();
    }

    /**
     * Get all quotations sorted by date descending.
     *
     * @param int $limit LIMIT statement part parameter
     * @param int $offset
     * @param string $o
     * @return array
     */
    public function findAllApproved($limit, $offset, $o)
    {
        if ($o == 'random') return $this->getRandomQuotes($limit);

        $q = $this->em->createQuery("
        select q, t, s, count(com.id) as hidden c
        from App\Model\Entities\Quote q
        left join q.teacher t
        left join q.subject s
        left join q.comments com
        where q.status=:status
        group by q.id
        {$this->order[$o]}
        ")->setFirstResult($offset)->setMaxResults($limit);

        $q->setParameter('status', Quote::STATUS_APPROVED);

        $q->useQueryCache(true);

        return $q->getResult();
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