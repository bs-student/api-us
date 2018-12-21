<?php
namespace AppBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * Country
 */
class Country
{

    public function __construct()
    {
        $this->states = new ArrayCollection();
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
    private $countryName;

    /**
     * @var string
     *
     */
    private $countryCode;

    /**
     * @var string
     *
     */
    private $countryCurrency;

    /**
     * @var string
     *
     */
    private $countryCurrencyShort;

    private $states;


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
     * Set countryName
     *
     * @param string $countryName
     * @return Country
     */
    public function setCountryName($countryName)
    {
        $this->countryName = $countryName;

        return $this;
    }

    /**
     * Get countryName
     *
     * @return string 
     */
    public function getCountryName()
    {
        return $this->countryName;
    }

    /**
     * Add states
     *
     * @param \AppBundle\Entity\State $states
     * @return Country
     */
    public function addState(\AppBundle\Entity\State $states)
    {
        $this->states[] = $states;

        return $this;
    }

    /**
     * Remove states
     *
     * @param \AppBundle\Entity\State $states
     */
    public function removeState(\AppBundle\Entity\State $states)
    {
        $this->states->removeElement($states);
    }

    /**
     * Get states
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getStates()
    {
        return $this->states;
    }

    /**
     * Set countryCode
     *
     * @param string $countryCode
     * @return Country
     */
    public function setCountryCode($countryCode)
    {
        $this->countryCode = $countryCode;

        return $this;
    }

    /**
     * Get countryCode
     *
     * @return string 
     */
    public function getCountryCode()
    {
        return $this->countryCode;
    }

    /**
     * Set countryCurrency
     *
     * @param string $countryCurrency
     * @return Country
     */
    public function setCountryCurrency($countryCurrency)
    {
        $this->countryCurrency = $countryCurrency;

        return $this;
    }

    /**
     * Get countryCurrency
     *
     * @return string 
     */
    public function getCountryCurrency()
    {
        return $this->countryCurrency;
    }

    /**
     * Set countryCurrencyShort
     *
     * @param string $countryCurrencyShort
     * @return Country
     */
    public function setCountryCurrencyShort($countryCurrencyShort)
    {
        $this->countryCurrencyShort = $countryCurrencyShort;

        return $this;
    }

    /**
     * Get countryCurrencyShort
     *
     * @return string 
     */
    public function getCountryCurrencyShort()
    {
        return $this->countryCurrencyShort;
    }

    public function __toString()
    {
        return $this->countryName;
    }
}
