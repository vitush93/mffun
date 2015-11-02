<?php

namespace App\Model\Entities;

use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\BaseEntity;

/**
 * @ORM\Entity
 */
class PasswordRecovery extends BaseEntity
{
    /**
     * Days for token to be alive.
     */
    const TOKEN_TTL = 14;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     * @var integer
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     * @var \DateTime
     */
    private $created;

    /**
     * @ORM\Column(type="string", unique=true)
     * @var string
     */
    private $token;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    private $email;

    /**
     * @ORM\Column(type="boolean")
     * @var bool
     */
    private $valid = TRUE;

    /**
     * @param $email
     */
    public function __construct($email)
    {
        $this->created = new \DateTime();
        $this->token = $this->generateToken();
        $this->email = $email;
    }

    private function generateToken()
    {
        return bin2hex(openssl_random_pseudo_bytes(16));
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param \DateTime $created
     */
    public function setCreated($created)
    {
        $this->created = $created;
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param string $token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return boolean
     */
    public function isValid()
    {
        return $this->valid;
    }

    public function invalidate()
    {
        $this->valid = FALSE;
    }

    public function isAlive()
    {
        $now = new \DateTime();
        $diff = $this->created->diff($now);

        return ($diff->days <= self::TOKEN_TTL);
    }


}