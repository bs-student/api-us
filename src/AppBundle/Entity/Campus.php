<?php
namespace AppBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * Campus
 */
class Campus
{

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }
    /**
     * @var integer
     *
     *
     */
    protected $id;

    /**
     * @var string
     *
     */
    private $campusName;


    private $university;

    /**
     * @var string
     *
     */
    private $campusStatus;
    private $state;

    private $users;



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
     * Set campusName
     *
     * @param string $campusName
     * @return Campus
     */
    public function setCampusName($campusName)
    {
        $this->campusName = $campusName;

        return $this;
    }

    /**
     * Get campusName
     *
     * @return string 
     */
    public function getCampusName()
    {
        return $this->campusName;
    }

    /**
     * Set university
     *
     * @param \AppBundle\Entity\University $university
     * @return Campus
     */
    public function setUniversity(\AppBundle\Entity\University $university = null)
    {
        $this->university = $university;

        return $this;
    }

    /**
     * Get university
     *
     * @return \AppBundle\Entity\University 
     */
    public function getUniversity()
    {
        return $this->university;
    }

    /**
     * Set state
     *
     * @param \AppBundle\Entity\State $state
     * @return Campus
     */
    public function setState(\AppBundle\Entity\State $state = null)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Get state
     *
     * @return \AppBundle\Entity\State 
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Add users
     *
     * @param \AppBundle\Entity\User $users
     * @return Campus
     */
    public function addUser(\AppBundle\Entity\User $users)
    {
        $this->users[] = $users;

        return $this;
    }

    /**
     * Remove users
     *
     * @param \AppBundle\Entity\User $users
     */
    public function removeUser(\AppBundle\Entity\User $users)
    {
        $this->users->removeElement($users);
    }

    /**
     * Get users
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUsers()
    {
        return $this->users;
    }


    /**
     * Set campusStatus
     *
     * @param string $campusStatus
     * @return Campus
     */
    public function setCampusStatus($campusStatus)
    {
        $this->campusStatus = $campusStatus;

        return $this;
    }

    /**
     * Get campusStatus
     *
     * @return string
     */
    public function getCampusStatus()
    {
        return $this->campusStatus;
    }



    public function __toString()
    {
        return strval($this->id);
    }
}
