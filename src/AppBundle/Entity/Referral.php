<?php
namespace AppBundle\Entity;


use Doctrine\ORM\Mapping as ORM;

/**
 * Referral
 */
class Referral
{
    /**
     * @var integer
     *
     */
    protected $id;

    /**
     * @var string
     *
     */
    private $referralName;



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
     * Set referralName
     *
     * @param string $referralName
     * @return Referral
     */
    public function setReferralName($referralName)
    {
        $this->referralName = $referralName;

        return $this;
    }

    /**
     * Get referralName
     *
     * @return string 
     */
    public function getReferralName()
    {
        return $this->referralName;
    }

    public function __toString()
    {
        return strval($this->id);
    }
}
