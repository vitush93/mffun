<?php

namespace App\Model\Entities;


use Doctrine\Common\Collections\ArrayCollection;
use Kdyby\Doctrine\Entities\BaseEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="subjects")
 */
class Subject extends BaseEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     * @var integer
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity="Quote", mappedBy="subject")
     */
    private $quotations;

    /**
     * @ORM\Column(type="string", unique=true)
     * @var string
     */
    private $name;

    public function __construct()
    {
        $this->quotations = new ArrayCollection();
    }

    /**
     * @param Quote $quote
     */
    public function addQuote($quote)
    {
        $this->quotations[] = $quote;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @param bool $asTag
     * @return string
     */
    public function getName($asTag = false)
    {
        if ($asTag) {
            $tag = explode(' ', $this->name);
            for ($i = 0; $i < count($tag); $i++) {
                $tag[$i] = ucfirst($tag[$i]);
            }
            $tag = implode('', $tag);
            return '#' . str_replace(' ', '', $tag);
        } else {
            return $this->name;
        }
    }

    /**
     * @return ArrayCollection
     */
    public function getQuotations()
    {
        return $this->quotations;
    }


} 