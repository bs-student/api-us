<?php
namespace AppBundle\Entity;

use FOS\UserBundle\Entity\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Entity\Campus;
use AppBundle\Entity\Referral;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * User
 */
class User extends BaseUser
{

    public function __construct()
    {
        $this->logs = new ArrayCollection();
        $this->buyBooks = new ArrayCollection();
        $this->sellBooks = new ArrayCollection();
        $this->wishLists = new ArrayCollection();
        $this->stars = new ArrayCollection();
        $this->contacts = new ArrayCollection();
        parent::__construct();
    }

    /**
     * @var integer
     */
    protected $id;

    /**
     * @var string
     *
     */
    private $fullName;


    /**
     * @var string
     *
     */
    private $googleId;

    /**
     * @var string
     *
     */
    private $googleEmail;

    /**
     * @var string
     *
     */
    private $googleToken;

    /**
     * @var string
     *
     */
    private $facebookId;

    /**
     * @var string
     *
     */
    private $facebookEmail;


    /**
     * @var string
     *
     */
    private $facebookToken;

    /**
     * @var string
     *
     */
    private $registrationStatus;
    /**
     * @var string
     *
     */
    private $emailVerified;
    /**
     * @var string
     *
     */
    private $adminVerified;
    /**
     * @var string
     *
     */
    private $adminApproved;

    /**
     * @var string
     *
     */
    private $standardHomePhone;

    /**
     * @var string
     *
     */
    private $standardCellPhone;

    /**
     * @var string
     *
     */
    private $standardEmail;

    /**
     * @var string
     *
     */
    private $profilePicture;

    private $registrationDateTime;

    protected $enabled;



    private $referral;

    private $campus;

    private $buyBooks;

    private $sellBooks;

    private $wishLists;

    private $contacts;

    private $stars;

    private $logs;

    private $emailNotification;

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
     * Set fullName
     *
     * @param string $fullName
     * @return User
     */
    public function setFullName($fullName)
    {
        $this->fullName = $fullName;

        return $this;
    }

    /**
     * Get fullName
     *
     * @return string 
     */
    public function getFullName()
    {
        return $this->fullName;
    }

    /**
     * Set googleId
     *
     * @param string $googleId
     * @return User
     */
    public function setGoogleId($googleId)
    {
        $this->googleId = $googleId;

        return $this;
    }

    /**
     * Get googleId
     *
     * @return string 
     */
    public function getGoogleId()
    {
        return $this->googleId;
    }

    /**
     * Set facebookId
     *
     * @param string $facebookId
     * @return User
     */
    public function setFacebookId($facebookId)
    {
        $this->facebookId = $facebookId;

        return $this;
    }

    /**
     * Get facebookId
     *
     * @return string 
     */
    public function getFacebookId()
    {
        return $this->facebookId;
    }


    /**
     * Set referral
     *
     * @param Referral $referral
     * @return User
     */
    public function setReferral(Referral $referral = null)
    {
        $this->referral = $referral;

        return $this;
    }

    /**
     * Get referral
     *
     * @return Referral
     */
    public function getReferral()
    {
        return $this->referral;
    }

    /**
     * Set campus
     *
     * @param Campus $campus
     * @return User
     */
    public function setCampus(Campus $campus = null)
    {
        $this->campus = $campus;

        return $this;
    }

    /**
     * Get campus
     *
     * @return Campus
     */
    public function getCampus()
    {
        return $this->campus;
    }

    

    /**
     * Set registrationStatus
     *
     * @param string $registrationStatus
     * @return User
     */
    public function setRegistrationStatus($registrationStatus)
    {
        $this->registrationStatus = $registrationStatus;

        return $this;
    }

    /**
     * Get registrationStatus
     *
     * @return string 
     */
    public function getRegistrationStatus()
    {
        return $this->registrationStatus;
    }

    /**
     * Set googleEmail
     *
     * @param string $googleEmail
     * @return User
     */
    public function setGoogleEmail($googleEmail)
    {
        $this->googleEmail = $googleEmail;

        return $this;
    }

    /**
     * Get googleEmail
     *
     * @return string 
     */
    public function getGoogleEmail()
    {
        return $this->googleEmail;
    }

    /**
     * Set googleToken
     *
     * @param string $googleToken
     * @return User
     */
    public function setGoogleToken($googleToken)
    {
        $this->googleToken = $googleToken;

        return $this;
    }

    /**
     * Get googleToken
     *
     * @return string 
     */
    public function getGoogleToken()
    {
        return $this->googleToken;
    }

    /**
     * Set facebookEmail
     *
     * @param string $facebookEmail
     * @return User
     */
    public function setFacebookEmail($facebookEmail)
    {
        $this->facebookEmail = $facebookEmail;

        return $this;
    }

    /**
     * Get facebookEmail
     *
     * @return string 
     */
    public function getFacebookEmail()
    {
        return $this->facebookEmail;
    }

    /**
     * Set facebookToken
     *
     * @param string $facebookToken
     * @return User
     */
    public function setFacebookToken($facebookToken)
    {
        $this->facebookToken = $facebookToken;

        return $this;
    }

    /**
     * Get facebookToken
     *
     * @return string 
     */
    public function getFacebookToken()
    {
        return $this->facebookToken;
    }

    /**
     * Set salt
     *
     * @param string $salt
     * @return User
     */
    public function setSalt($salt) {
        $this->salt = $salt;
        return $this;
    }




    /**
     * Add buyBooks
     *
     * @param \AppBundle\Entity\Book $buyBooks
     * @return User
     */
    public function addBuyBook(\AppBundle\Entity\Book $buyBooks)
    {
        $this->buyBooks[] = $buyBooks;

        return $this;
    }

    /**
     * Remove buyBooks
     *
     * @param \AppBundle\Entity\Book $buyBooks
     */
    public function removeBuyBook(\AppBundle\Entity\Book $buyBooks)
    {
        $this->buyBooks->removeElement($buyBooks);
    }

    /**
     * Get buyBooks
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getBuyBooks()
    {
        return $this->buyBooks;
    }

    /**
     * Add sellBooks
     *
     * @param \AppBundle\Entity\Book $sellBooks
     * @return User
     */
    public function addSellBook(\AppBundle\Entity\Book $sellBooks)
    {
        $this->sellBooks[] = $sellBooks;

        return $this;
    }

    /**
     * Remove sellBooks
     *
     * @param \AppBundle\Entity\Book $sellBooks
     */
    public function removeSellBook(\AppBundle\Entity\Book $sellBooks)
    {
        $this->sellBooks->removeElement($sellBooks);
    }

    /**
     * Get sellBooks
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSellBooks()
    {
        return $this->sellBooks;
    }

    /**
     * Add messages
     *
     * @param \AppBundle\Entity\Message $messages
     * @return User
     */
    public function addMessage(\AppBundle\Entity\Message $messages)
    {
        $this->messages[] = $messages;

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
     * Add wishLists
     *
     * @param \AppBundle\Entity\WishList $wishLists
     * @return User
     */
    public function addWishList(\AppBundle\Entity\WishList $wishLists)
    {
        $this->wishLists->add($wishLists);
        $wishLists->setUser($this);
        return $this;

    }

    /**
     * Remove wishLists
     *
     * @param \AppBundle\Entity\WishList $wishLists
     */
    public function removeWishList(\AppBundle\Entity\WishList $wishLists)
    {
        $this->wishLists->removeElement($wishLists);
    }

    /**
     * Get wishLists
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getWishLists()
    {
        return $this->wishLists;
    }

    /**
     * Add contacts
     *
     * @param \AppBundle\Entity\Contact $contacts
     * @return User
     */
    public function addContact(\AppBundle\Entity\Contact $contacts)
    {
        $this->contacts[] = $contacts;

        return $this;
    }

    /**
     * Remove contacts
     *
     * @param \AppBundle\Entity\Contact $contacts
     */
    public function removeContact(\AppBundle\Entity\Contact $contacts)
    {
        $this->contacts->removeElement($contacts);
    }

    /**
     * Get contacts
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getContacts()
    {
        return $this->contacts;
    }

    /**
     * Set adminApproved
     *
     * @param string $adminApproved
     * @return User
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
     * Set standardHomePhone
     *
     * @param string $standardHomePhone
     * @return User
     */
    public function setStandardHomePhone($standardHomePhone)
    {
        $this->standardHomePhone = $standardHomePhone;

        return $this;
    }

    /**
     * Get standardHomePhone
     *
     * @return string 
     */
    public function getStandardHomePhone()
    {
        return $this->standardHomePhone;
    }

    /**
     * Set standardCellPhone
     *
     * @param string $standardCellPhone
     * @return User
     */
    public function setStandardCellPhone($standardCellPhone)
    {
        $this->standardCellPhone = $standardCellPhone;

        return $this;
    }

    /**
     * Get standardCellPhone
     *
     * @return string 
     */
    public function getStandardCellPhone()
    {
        return $this->standardCellPhone;
    }

    /**
     * Set standardEmail
     *
     * @param string $standardEmail
     * @return User
     */
    public function setStandardEmail($standardEmail)
    {
        $this->standardEmail = $standardEmail;

        return $this;
    }

    /**
     * Get standardEmail
     *
     * @return string 
     */
    public function getStandardEmail()
    {
        return $this->standardEmail;
    }

    /**
     * Set profilePicture
     *
     * @param string $profilePicture
     * @return User
     */
    public function setProfilePicture($profilePicture)
    {
        $this->profilePicture = $profilePicture;

        return $this;
    }

    /**
     * Get profilePicture
     *
     * @return string 
     */
    public function getProfilePicture()
    {
        return $this->profilePicture;
    }

    /**
     * Add stars
     *
     * @param \AppBundle\Entity\Star $stars
     * @return User
     */
    public function addStar(\AppBundle\Entity\Star $stars)
    {
        $this->stars->add($stars);
        $stars->setUser($this);
        return $this;

    }

    /**
     * Remove stars
     *
     * @param \AppBundle\Entity\Star $stars
     */
    public function removeStar(\AppBundle\Entity\Star $stars)
    {
        $this->stars->removeElement($stars);
    }

    /**
     * Get stars
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getStars()
    {
        return $this->stars;
    }

    /**
     * Set emailNotification
     *
     * @param string $emailNotification
     * @return User
     */
    public function setEmailNotification($emailNotification)
    {
        $this->emailNotification = $emailNotification;

        return $this;
    }

    /**
     * Get emailNotification
     *
     * @return string 
     */
    public function getEmailNotification()
    {
        return $this->emailNotification;
    }

    /**
     * Set registrationDateTime
     *
     * @param \DateTime $registrationDateTime
     * @return User
     */
    public function setRegistrationDateTime($registrationDateTime)
    {
        $this->registrationDateTime = $registrationDateTime;

        return $this;
    }

    /**
     * Get registrationDateTime
     *
     * @return \DateTime 
     */
    public function getRegistrationDateTime()
    {
        return $this->registrationDateTime;
    }

    /**
     * Add logs
     *
     * @param \AppBundle\Entity\Log $logs
     * @return User
     */
    public function addLog(\AppBundle\Entity\Log $logs)
    {
        $this->logs[] = $logs;
//        $this->logs->add($logs);
//        $logs->setUser($this);
        return $this;
    }

    /**
     * Remove logs
     *
     * @param \AppBundle\Entity\Log $logs
     */
    public function removeLog(\AppBundle\Entity\Log $logs)
    {
        $this->logs->removeElement($logs);
    }

    /**
     * Get logs
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getLogs()
    {
        return $this->logs;
    }

    /**
     * Set emailVerified
     *
     * @param string $emailVerified
     * @return User
     */
    public function setEmailVerified($emailVerified)
    {
        $this->emailVerified = $emailVerified;

        return $this;
    }

    /**
     * Get emailVerified
     *
     * @return string 
     */
    public function getEmailVerified()
    {
        return $this->emailVerified;
    }

    /**
     * Set adminVerified
     *
     * @param string $adminVerified
     * @return User
     */
    public function setAdminVerified($adminVerified)
    {
        $this->adminVerified = $adminVerified;

        return $this;
    }

    /**
     * Get adminVerified
     *
     * @return string 
     */
    public function getAdminVerified()
    {
        return $this->adminVerified;
    }
}
