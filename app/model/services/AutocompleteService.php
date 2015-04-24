<?php
/**
 * Created by PhpStorm.
 * User: vitush
 * Date: 3/29/15
 * Time: 2:43 PM
 */

namespace App\Model\Services;


use App\Model\Entities\Subject;
use App\Model\Entities\Tag;
use App\Model\Entities\Teacher;
use App\Model\Repositories\QuoteRepository;
use Kdyby\Doctrine\EntityManager;
use Nette\Object;

class AutocompleteService extends Object
{
    /** @var QuoteRepository */
    private $quoteRepository;

    /** @var EntityManager */
    private $em;

    public function __construct(QuoteRepository $quoteRepository, EntityManager $entityManager)
    {
        $this->quoteRepository = $quoteRepository;
        $this->em = $entityManager;
    }

    /**
     * @return array
     */
    public function getTagsAsArray()
    {
        $tags = array();

        /** @var Tag $t */
        foreach($this->em->getRepository(Tag::getClassName())->findAll() as $t) {
            $tags[] = $t->getTag();
        }

        return $tags;
    }

    /**
     * @return array
     */
    public function getSubjectsAsArray()
    {
        $subj = array();

        /** @var Subject $subject */
        foreach($this->em->getRepository(Subject::getClassName())->findAll() as $subject) {
            $subj[] = $subject->getName();
        }

        return $subj;
    }

    /**
     * @return array
     */
    public function getTeachersAsArray()
    {
        $t = array();

        /** @var Teacher $teacher */
        foreach($this->em->getRepository(Teacher::getClassName())->findAll() as $teacher) {
            $t[] = $teacher->getName();
        }

        return $t;
    }
}