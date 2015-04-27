<?php

namespace App\Model\Entities;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\BaseEntity;

/**
 * @ORM\Entity
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

    /**
     * @param string $tag
     */
    public function __construct($tag)
    {
        $this->setTag($tag);
        $this->quotations = new ArrayCollection();
    }

    /**
     * @param Quote $quote
     */
    public function assignToQuote($quote)
    {
        $this->quotations[] = $quote;
        $quote->addTag($this);
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