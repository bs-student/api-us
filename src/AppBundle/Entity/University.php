<?php
namespace AppBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * University
 */
class University
{
    public function __construct()
    {
        $this->campuses = new ArrayCollection();
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
    private $universityName;


    /**
     * @var string
     *
     */
    private $universityUrl;

    /**
     * @var string
     *
     */
    private $universityStatus;

    /**
     * @var string
     *
     */
    private $adminApproved;

    /**
     * @var datetime
     */
    private $creationDateTime;

    private $referral;



    private $campuses;


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
     * Set universityName
     *
     * @param string $universityName
     * @return University
     */
    public function setUniversityName($universityName)
    {
        $this->universityName = $universityName;

        return $this;
    }

    /**
     * Get universityName
     *
     * @return string 
     */
    public function getUniversityName()
    {
        return $this->universityName;
    }

    /**
     * Set universityUrl
     *
     * @param string $universityUrl
     * @return University
     */
    public function setUniversityUrl($universityUrl)
    {
        $this->universityUrl = $universityUrl;

        return $this;
    }

    /**
     * Get universityUrl
     *
     * @return string 
     */
    public function getUniversityUrl()
    {
        return $this->universityUrl;
    }

    /**
     * Set referral
     *
     * @param \AppBundle\Entity\Referral $referral
     * @return University
     */
    public function setReferral(\AppBundle\Entity\Referral $referral = null)
    {
        $this->referral = $referral;

        return $this;
    }

    /**
     * Get referral
     *
     * @return \AppBundle\Entity\Referral 
     */
    public function getReferral()
    {
        return $this->referral;
    }

    /**
     * Add campuses
     *
     * @param \AppBundle\Entity\Campus $campus
     * @return University
     */
    public function addCampus(\AppBundle\Entity\Campus $campus)
    {
        $this->campuses->add($campus);
        $campus->setUniversity($this);
        return $this;
    }



    /**
     * Remove campuses
     *
     * @param \AppBundle\Entity\Campus $campuses
     */
    public function removeCampus(\AppBundle\Entity\Campus $campuses)
    {
        $this->campuses->removeElement($campuses);
    }

    /**
     * Get campuses
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCampuses()
    {
        return $this->campuses;
    }


//    public function setCampuses($campuses)
//    {
//        $this->campuses = $campuses;
//
//        return $this;
//    }


    public function __toString()
    {
        return strval($this->id);
    }

    /**
     * Set universityStatus
     *
     * @param string $universityStatus
     * @return University
     */
    public function setUniversityStatus($universityStatus)
    {
        $this->universityStatus = $universityStatus;

        return $this;
    }

    /**
     * Get universityStatus
     *
     * @return string 
     */
    public function getUniversityStatus()
    {
        return $this->universityStatus;
    }

    /**
     * Set adminApproved
     *
     * @param string $adminApproved
     * @return University
     */
    public function setAdminApproved($adminApproved)
    {
        $this->adminApproved = $adminApproved;

        return $this;
    }

    /**
     * Get adminApproved
     *
     * @return string 
     */
    public function getAdminApproved()
    {
        return $this->adminApproved;
    }

    /**
     * Set creationDateTime
     *
     * @param \DateTime $creationDateTime
     * @return University
     */
    public function setCreationDateTime($creationDateTime)
    {
        $this->creationDateTime = $creationDateTime;

        return $this;
    }

    /**
     * Get creationDateTime
     *
     * @return \DateTime 
     */
    public function getCreationDateTime()
    {
        return $this->creationDateTime;
    }
}
