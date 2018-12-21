<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Log
 */
class Log
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $logType;

    /**
     * @var string
     */
    private $logUserType;

    /**
     * @var \DateTime
     */
    private $logDateTime;

    /**
     * @var string
     */
    private $logDescription;

    /**
     * @var string
     */
    private $userIpAddress;

    private $user;


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
     * Set logType
     *
     * @param string $logType
     * @return Log
     */
    public function setLogType($logType)
    {
        $this->logType = $logType;

        return $this;
    }

    /**
     * Get logType
     *
     * @return string 
     */
    public function getLogType()
    {
        return $this->logType;
    }

    /**
     * Set logDateTime
     *
     * @param \DateTime $logDateTime
     * @return Log
     */
    public function setLogDateTime($logDateTime)
    {
        $this->logDateTime = $logDateTime;

        return $this;
    }

    /**
     * Get logDateTime
     *
     * @return \DateTime 
     */
    public function getLogDateTime()
    {
        return $this->logDateTime;
    }

    /**
     * Set logDescription
     *
     * @param string $logDescription
     * @return Log
     */
    public function setLogDescription($logDescription)
    {
        $this->logDescription = $logDescription;

        return $this;
    }

    /**
     * Get logDescription
     *
     * @return string 
     */
    public function getLogDescription()
    {
        return $this->logDescription;
    }
    /**
     * @var \AppBundle\Entity\User
     */
    


    /**
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     * @return Log
     */
    public function setUser(\AppBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \AppBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    public function  __toString(){
        return (string)$this->id;
    }

    /**
     * Set userIpAddress
     *
     * @param string $userIpAddress
     * @return Log
     */
    public function setUserIpAddress($userIpAddress)
    {
        $this->userIpAddress = $userIpAddress;

        return $this;
    }

    /**
     * Get userIpAddress
     *
     * @return string 
     */
    public function getUserIpAddress()
    {
        return $this->userIpAddress;
    }

    /**
     * Set logUserType
     *
     * @param string $logUserType
     * @return Log
     */
    public function setLogUserType($logUserType)
    {
        $this->logUserType = $logUserType;

        return $this;
    }

    /**
     * Get logUserType
     *
     * @return string 
     */
    public function getLogUserType()
    {
        return $this->logUserType;
    }
}
