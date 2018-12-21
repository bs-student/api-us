<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * Contact
 */
class Contact
{
    public function __construct()
    {
        $this->messages = new ArrayCollection();
    }

    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $buyerNickName;

    /**
     * @var string
     */
    private $buyerEmail;
    /**
     * @var string
     */
    private $buyerHomePhone;
    /**
     * @var string
     */
    private $buyerCellPhone;

    /**
     * @var string
     */
    private $soldToThatBuyer;

    /**
     * @var datetime
     */
    private $contactDateTime;

    /**
     * @var string
     */
    private $contactCondition;

    private $bookDeal;

    private $buyer;

    private $messages;



    public function __toString()
    {
        return strval($this->id);
    }

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
     * Set buyerNickName
     *
     * @param string $buyerNickName
     * @return Contact
     */
    public function setBuyerNickName($buyerNickName)
    {
        $this->buyerNickName = $buyerNickName;

        return $this;
    }

    /**
     * Get buyerNickName
     *
     * @return string 
     */
    public function getBuyerNickName()
    {
        return $this->buyerNickName;
    }

    /**
     * Set buyerEmail
     *
     * @param string $buyerEmail
     * @return Contact
     */
    public function setBuyerEmail($buyerEmail)
    {
        $this->buyerEmail = $buyerEmail;

        return $this;
    }

    /**
     * Get buyerEmail
     *
     * @return string 
     */
    public function getBuyerEmail()
    {
        return $this->buyerEmail;
    }

    /**
     * Set bookDeal
     *
     * @param \AppBundle\Entity\BookDeal $bookDeal
     * @return Contact
     */
    public function setBookDeal(\AppBundle\Entity\BookDeal $bookDeal = null)
    {
        $this->bookDeal = $bookDeal;

        return $this;
    }

    /**
     * Get bookDeal
     *
     * @return \AppBundle\Entity\BookDeal 
     */
    public function getBookDeal()
    {
        return $this->bookDeal;
    }

    /**
     * Set buyer
     *
     * @param \AppBundle\Entity\User $buyer
     * @return Contact
     */
    public function setBuyer(\AppBundle\Entity\User $buyer = null)
    {
        $this->buyer = $buyer;

        return $this;
    }

    /**
     * Get buyer
     *
     * @return \AppBundle\Entity\User 
     */
    public function getBuyer()
    {
        return $this->buyer;
    }

    /**
     * Add messages
     *
     * @param \AppBundle\Entity\Message $messages
     * @return Contact
     */
    public function addMessage(\AppBundle\Entity\Message $messages)
    {

        $this->messages->add($messages);
        $messages->setContact($this);
        return $this;

    }

    /**
     * Remove messages
     *
     * @param \AppBundle\Entity\Message $messages
     */
    public function removeMessage(\AppBundle\Entity\Message $messages)
    {
        $this->messages->removeElement($messages);
    }

    /**
     * Get messages
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * Set buyerHomePhone
     *
     * @param string $buyerHomePhone
     * @return Contact
     */
    public function setBuyerHomePhone($buyerHomePhone)
    {
        $this->buyerHomePhone = $buyerHomePhone;

        return $this;
    }

    /**
     * Get buyerHomePhone
     *
     * @return string 
     */
    public function getBuyerHomePhone()
    {
        return $this->buyerHomePhone;
    }

    /**
     * Set buyerCellPhone
     *
     * @param string $buyerCellPhone
     * @return Contact
     */
    public function setBuyerCellPhone($buyerCellPhone)
    {
        $this->buyerCellPhone = $buyerCellPhone;

        return $this;
    }

    /**
     * Get buyerCellPhone
     *
     * @return string 
     */
    public function getBuyerCellPhone()
    {
        return $this->buyerCellPhone;
    }

    /**
     * Set contactDateTime
     *
     * @param \DateTime $contactDateTime
     * @return Contact
     */
    public function setContactDateTime($contactDateTime)
    {
        $this->contactDateTime = $contactDateTime;

        return $this;
    }

    /**
     * Get contactDateTime
     *
     * @return \DateTime 
     */
    public function getContactDateTime()
    {
        return $this->contactDateTime;
    }

    /**
     * Set soldToThatBuyer
     *
     * @param string $soldToThatBuyer
     * @return Contact
     */
    public function setSoldToThatBuyer($soldToThatBuyer)
    {
        $this->soldToThatBuyer = $soldToThatBuyer;

        return $this;
    }

    /**
     * Get soldToThatBuyer
     *
     * @return string 
     */
    public function getSoldToThatBuyer()
    {
        return $this->soldToThatBuyer;
    }

    /**
     * Set contactCondition
     *
     * @param string $contactCondition
     * @return Contact
     */
    public function setContactCondition($contactCondition)
    {
        $this->contactCondition = $contactCondition;

        return $this;
    }

    /**
     * Get contactCondition
     *
     * @return string 
     */
    public function getContactCondition()
    {
        return $this->contactCondition;
    }
}
