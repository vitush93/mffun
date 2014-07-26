<?php

namespace App\Model\Entities;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\BaseEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="articles")
 */
class Article extends BaseEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity="Comment",mappedBy="article")
     */
    private $comments;

    /**
     * @ORM\Column(type="string")
     */
    private $title;

    /**
     * @ORM\ManyToOne(targetEntity="User",inversedBy="articles")
     */
    private $author;

    /**
     * @ORM\Column(type="datetime")
     */
    private $posted;

    /**
     * @ORM\Column(type="text")
     */
    private $text;

    /**
     * @ORM\Column(type="boolean")
     */
    private $published = FALSE;

    public function __construct()
    {
        $this->posted = new \DateTime();
        $this->comments = new ArrayCollection();
    }

    public function addComment(Comment $comment)
    {
        $this->comments[] = $comment;
    }

    public function getId()
    {
        return $this->id;
    }

    public function  getTitle()
    {
        return $this->title;
    }

    public function getText()
    {
        return $this->text;
    }

    public function setText($text)
    {
        $this->text = $text;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getAuthor()
    {
        return $this->author;
    }

    public function setAuthor($author)
    {
        $this->author = $author;
    }
    
    public function getPosted()
    {
        return $this->posted;
    }

    public function unpublish()
    {
        $this->published = FALSE;
    }

    public function publish()
    {
        $this->published = TRUE;
    }
}