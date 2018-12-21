<?php
namespace AppBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * State
 */
class State
{
    public function __construct()
    {
        $this->campuses = new ArrayCollection();
    }

    /**
     * @var integer
     *
     */
    protected $id;

    /**
     * @var string
     *
     */
    private $stateName;

    /**
     * @var string
     *
     */
    private $stateShortName;

    protected $country;

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
     * Set stateName
     *
     * @param string $stateName
     * @return State
     */
    public function setStateName($stateName)
    {
        $this->stateName = $stateName;

        return $this;
    }

    /**
     * Get stateName
     *
     * @return string 
     */
    public function getStateName()
    {
        return $this->stateName;
    }

    /**
     * Set stateShortName
     *
     * @param string $stateShortName
     * @return State
     */
    public function setStateShortName($stateShortName)
    {
        $this->stateShortName = $stateShortName;

        return $this;
    }

    /**
     * Get stateShortName
     *
     * @return string 
     */
    public function getStateShortName()
    {
        return $this->stateShortName;
    }

    /**
     * Set country
     *
     * @param \AppBundle\Entity\Country $country
     * @return State
     */
    public function setCountry(\AppBundle\Entity\Country $country = null)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return \AppBundle\Entity\Country 
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Add campuses
     *
     * @param \AppBundle\Entity\Campus $campuses
     * @return State
     */
    public function addCampus(\AppBundle\Entity\Campus $campuses)
    {
        $this->campuses[] = $campuses;

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
    public function  __toString(){
        return $this->stateName;
    }
}
