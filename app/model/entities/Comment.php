<?php

namespace App\Model\Entities;

use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\BaseEntity;
use DateTime;

/**
 * @ORM\Entity
 * @ORM\Table(name="comments")
 */
class Comment extends BaseEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     * @var integer
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="comments")
     * @var User
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="Quote", inversedBy="comments")
     * @var Quote
     */
    private $quote;

    // TODO entities & relationships
    private $ratings;

    /**
     * @ORM\Column(type="text")
     * @var string
     */
    private $text;

    /**
     * @ORM\Column(type="datetime")
     * @var DateTime
     */
    private $posted;

    /**
     * @ORM\Column(type="integer")
     * @var int
     */
    private $parent = 0;

    public function __construct()
    {
        $this->posted = new DateTime();
    }

} 