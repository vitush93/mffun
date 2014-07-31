<?php

namespace App\Model\Entities;


use Doctrine\Common\Collections\ArrayCollection;
use Kdyby\Doctrine\Entities\BaseEntity;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\OneToMany;

/**
 * @ORM\Entity
 * @ORM\Table(name="teachers")
 */
class Teacher extends BaseEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     * @var integer
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity="Quote", mappedBy="teacher")
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

    public function addQuote(Quote $quote)
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
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getQuotations()
    {
        return $this->quotations;
    }




}