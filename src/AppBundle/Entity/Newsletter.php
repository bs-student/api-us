<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Newsletter
 */
class Newsletter
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $email;

    /**
     * @var \DateTime
     */
    private $lastUpdateDateTime;

    /**
     * @var string
     */
    private $activationStatus;



    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return Newsletter
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set lastUpdateDateTime
     *
     * @param \DateTime $lastUpdateDateTime
     * @return Newsletter
     */
    public function setLastUpdateDateTime($lastUpdateDateTime)
    {
        $this->lastUpdateDateTime = $lastUpdateDateTime;

        return $this;
    }

    /**
     * Get lastUpdateDateTime
     *
     * @return \DateTime 
     */
    public function getLastUpdateDateTime()
    {
        return $this->lastUpdateDateTime;
    }

    /**
     * Set activationStatus
     *
     * @param string $activationStatus
     * @return Newsletter
     */
    public function setActivationStatus($activationStatus)
    {
        $this->activationStatus = $activationStatus;

        return $this;
    }

    /**
     * Get activationStatus
     *
     * @return string 
     */
    public function getActivationStatus()
    {
        return $this->activationStatus;
    }
}
