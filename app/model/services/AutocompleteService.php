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

    public function getSearchBoxData()
    {
        $subjects = $this->em->getRepository(Subject::class)->findAll();
        $teachers = $this->em->getRepository(Teacher::class)->findAll();
        $tags = $this->em->getRepository(Tag::class)->findAll();

        $result = [];
        $i = 0;
        /** @var Subject $s */
        foreach ($subjects as $s) {
            $result[$i]['value'] = $s->getName();
            $result[$i]['label'] = $s->getNormalized().' '.$s->getName();
            $result[$i]['desc'] = self::tagize($s->getName());
            $result[$i]['type'] = 'subject';
            $result[$i++]['id'] = $s->getId();
        }

        /** @var Teacher $s */
        foreach ($teachers as $s) {
            $result[$i]['value'] = $s->getName();
            $result[$i]['label'] = $s->getNormalized().' '.$s->getName();
            $result[$i]['desc'] = self::tagize($s->getName());
            $result[$i]['type'] = 'teacher';
            $result[$i++]['id'] = $s->getId();
        }

        /** @var Tag $s */
        foreach ($tags as $s) {
            $result[$i]['value'] = $s->getTag();
            $result[$i]['label'] = $s->getNormalized().' '.$s->getTag();
            $result[$i]['desc'] = self::tagize($s->getTag());
            $result[$i]['type'] = 'tag';
            $result[$i++]['id'] = $s->getId();
        }

        return $result;
    }

    private static function tagize($item)
    {
        $tag = explode(' ', $item);
        for ($i = 0; $i < count($tag); $i++) {
            if (count($tag) > 1) $tag[$i] = ucfirst($tag[$i]);
        }
        $tag = implode('', $tag);

        return '#' . str_replace(' ', '', $tag);
    }

    /**
     * @return array
     */
    public function getTagsAsArray()
    {
        $tags = array();

        /** @var Tag $t */
        foreach ($this->em->getRepository(Tag::getClassName())->findAll() as $t) {
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
        foreach ($this->em->getRepository(Subject::getClassName())->findAll() as $subject) {
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
        foreach ($this->em->getRepository(Teacher::getClassName())->findAll() as $teacher) {
            $t[] = $teacher->getName();
        }

        return $t;
    }
}