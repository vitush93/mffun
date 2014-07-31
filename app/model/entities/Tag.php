<?php

namespace App\Model\Entities;


use Doctrine\Common\Collections\ArrayCollection;
use Kdyby\Doctrine\Entities\BaseEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="tags")
 */
class Tag extends BaseEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     * @var integer
     */
    private $id;

    /**
     * @ORM\ManyToMany(targetEntity="Quote")
     */
    private $quotations = NULL;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    private $tag;

    public function __construct()
    {
        $this->quotations = new ArrayCollection();
    }

    /**
     * @param Quote $quote
     */
    public function assignToQuote(Quote $quote)
    {
        $this->quotations[] = $quote;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getQuotations()
    {
        return $this->quotations;
    }

    /**
     * @param string $tag
     */
    public function setTag($tag)
    {
        $this->tag = $tag;
    }

    /**
     * @return string
     */
    public function getTag()
    {
        return $this->tag;
    }



} 