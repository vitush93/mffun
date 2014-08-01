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
     * @ORM\ManyToMany(targetEntity="Quote", inversedBy="tags")
     * @var ArrayCollection
     */
    private $quotations;

    /**
     * @ORM\Column(type="string", unique=true)
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
    public function assignToQuote($quote)
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
     * @return ArrayCollection
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