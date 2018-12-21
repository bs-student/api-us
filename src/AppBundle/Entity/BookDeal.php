<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * BookDeal
 */
class BookDeal
{
    public function __construct()
    {
        $this->contacts = new ArrayCollection();
        $this->bookDealImages = new ArrayCollection();
    }

    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $bookPriceSell;

    /**
     * @var string
     */
    private $bookCondition;

    /**
     * @var string
     */
    private $bookIsHighlighted;

    /**
     * @var string
     */
    private $bookHasNotes;

    /**
     * @var string
     */
    private $bookComment;

    /**
     * @var string
     */
    private $bookContactMethod;

    /**
     * @var string
     */
    private $bookContactHomeNumber;

    /**
     * @var string
     */
    private $bookContactCellNumber;

    /**
     * @var string
     */
    private $bookContactEmail;

    /**
     * @var string
     */
    private $bookIsAvailablePublic;

    /**
     * @var boolean
     */
    private $bookPaymentMethodCashOnExchange;

    /**
     * @var boolean
     */
    private $bookPaymentMethodCheque;

    /**
     * @var \DateTime
     */
    private $bookAvailableDate;

    /**
     * @var string
     */
    private $bookSellingStatus;

    /**
     * @var string
     */
    private $bookStatus;

    /**
     * @var integer
     */
    private $bookViewCount;

    /**
     * @var \DateTime
     */
    private $bookSubmittedDateTime;

    private $book;

    private $contacts;

    private $bookDealImages;

    private $seller;

    private $buyer;


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
     * Set bookPriceSell
     *
     * @param string $bookPriceSell
     * @return BookDeal
     */
    public function setBookPriceSell($bookPriceSell)
    {
        $this->bookPriceSell = $bookPriceSell;

        return $this;
    }

    /**
     * Get bookPriceSell
     *
     * @return string 
     */
    public function getBookPriceSell()
    {
        return $this->bookPriceSell;
    }

    /**
     * Set bookCondition
     *
     * @param string $bookCondition
     * @return BookDeal
     */
    public function setBookCondition($bookCondition)
    {
        $this->bookCondition = $bookCondition;

        return $this;
    }

    /**
     * Get bookCondition
     *
     * @return string 
     */
    public function getBookCondition()
    {
        return $this->bookCondition;
    }

    /**
     * Set bookIsHighlighted
     *
     * @param string $bookIsHighlighted
     * @return BookDeal
     */
    public function setBookIsHighlighted($bookIsHighlighted)
    {
        $this->bookIsHighlighted = $bookIsHighlighted;

        return $this;
    }

    /**
     * Get bookIsHighlighted
     *
     * @return string 
     */
    public function getBookIsHighlighted()
    {
        return $this->bookIsHighlighted;
    }

    /**
     * Set bookHasNotes
     *
     * @param string $bookHasNotes
     * @return BookDeal
     */
    public function setBookHasNotes($bookHasNotes)
    {
        $this->bookHasNotes = $bookHasNotes;

        return $this;
    }

    /**
     * Get bookHasNotes
     *
     * @return string 
     */
    public function getBookHasNotes()
    {
        return $this->bookHasNotes;
    }

    /**
     * Set bookComment
     *
     * @param string $bookComment
     * @return BookDeal
     */
    public function setBookComment($bookComment)
    {
        $this->bookComment = $bookComment;

        return $this;
    }

    /**
     * Get bookComment
     *
     * @return string 
     */
    public function getBookComment()
    {
        return $this->bookComment;
    }

    /**
     * Set bookContactMethod
     *
     * @param string $bookContactMethod
     * @return BookDeal
     */
    public function setBookContactMethod($bookContactMethod)
    {
        $this->bookContactMethod = $bookContactMethod;

        return $this;
    }

    /**
     * Get bookContactMethod
     *
     * @return string 
     */
    public function getBookContactMethod()
    {
        return $this->bookContactMethod;
    }

    /**
     * Set bookContactHomeNumber
     *
     * @param string $bookContactHomeNumber
     * @return BookDeal
     */
    public function setBookContactHomeNumber($bookContactHomeNumber)
    {
        $this->bookContactHomeNumber = $bookContactHomeNumber;

        return $this;
    }

    /**
     * Get bookContactHomeNumber
     *
     * @return string 
     */
    public function getBookContactHomeNumber()
    {
        return $this->bookContactHomeNumber;
    }

    /**
     * Set bookContactCellNumber
     *
     * @param string $bookContactCellNumber
     * @return BookDeal
     */
    public function setBookContactCellNumber($bookContactCellNumber)
    {
        $this->bookContactCellNumber = $bookContactCellNumber;

        return $this;
    }

    /**
     * Get bookContactCellNumber
     *
     * @return string 
     */
    public function getBookContactCellNumber()
    {
        return $this->bookContactCellNumber;
    }

    /**
     * Set bookContactEmail
     *
     * @param string $bookContactEmail
     * @return BookDeal
     */
    public function setBookContactEmail($bookContactEmail)
    {
        $this->bookContactEmail = $bookContactEmail;

        return $this;
    }

    /**
     * Get bookContactEmail
     *
     * @return string 
     */
    public function getBookContactEmail()
    {
        return $this->bookContactEmail;
    }

    /**
     * Set bookIsAvailablePublic
     *
     * @param string $bookIsAvailablePublic
     * @return BookDeal
     */
    public function setBookIsAvailablePublic($bookIsAvailablePublic)
    {
        $this->bookIsAvailablePublic = $bookIsAvailablePublic;

        return $this;
    }

    /**
     * Get bookIsAvailablePublic
     *
     * @return string 
     */
    public function getBookIsAvailablePublic()
    {
        return $this->bookIsAvailablePublic;
    }



    /**
     * Set bookPaymentMethodCheque
     *
     * @param boolean $bookPaymentMethodCheque
     * @return BookDeal
     */
    public function setBookPaymentMethodCheque($bookPaymentMethodCheque)
    {
        $this->bookPaymentMethodCheque = $bookPaymentMethodCheque;

        return $this;
    }

    /**
     * Get bookPaymentMethodCheque
     *
     * @return boolean 
     */
    public function getBookPaymentMethodCheque()
    {
        return $this->bookPaymentMethodCheque;
    }

    /**
     * Set bookAvailableDate
     *
     * @param \DateTime $bookAvailableDate
     * @return BookDeal
     */
    public function setBookAvailableDate($bookAvailableDate)
    {
        $this->bookAvailableDate = $bookAvailableDate;

        return $this;
    }

    /**
     * Get bookAvailableDate
     *
     * @return \DateTime 
     */
    public function getBookAvailableDate()
    {
        return $this->bookAvailableDate;
    }

    /**
     * Set bookSellingStatus
     *
     * @param string $bookSellingStatus
     * @return BookDeal
     */
    public function setBookSellingStatus($bookSellingStatus)
    {
        $this->bookSellingStatus = $bookSellingStatus;

        return $this;
    }

    /**
     * Get bookSellingStatus
     *
     * @return string 
     */
    public function getBookSellingStatus()
    {
        return $this->bookSellingStatus;
    }

    /**
     * Set bookViewCount
     *
     * @param integer $bookViewCount
     * @return BookDeal
     */
    public function setBookViewCount($bookViewCount)
    {
        $this->bookViewCount = $bookViewCount;

        return $this;
    }

    /**
     * Get bookViewCount
     *
     * @return integer 
     */
    public function getBookViewCount()
    {
        return $this->bookViewCount;
    }

    /**
     * Set bookStatus
     *
     * @param string $bookStatus
     * @return BookDeal
     */
    public function setBookStatus($bookStatus)
    {
        $this->bookStatus = $bookStatus;

        return $this;
    }

    /**
     * Get bookStatus
     *
     * @return string 
     */
    public function getBookStatus()
    {
        return $this->bookStatus;
    }

    /**
     * Add contacts
     *
     * @param \AppBundle\Entity\Contact $contacts
     * @return BookDeal
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
     * Add bookDealImages
     *
     * @param \AppBundle\Entity\BookDealImage $bookDealImages
     * @return BookDeal
     */
    public function addBookDealImage(\AppBundle\Entity\BookDealImage $bookDealImages)
    {
        $this->bookDealImages->add($bookDealImages);
        $bookDealImages->setBookDeal($this);
        return $this;

    }

    /**
     * Remove bookDealImages
     *
     * @param \AppBundle\Entity\BookDealImage $bookDealImages
     */
    public function removeBookDealImage(\AppBundle\Entity\BookDealImage $bookDealImages)
    {
        $this->bookDealImages->removeElement($bookDealImages);
        $bookDealImages->removeBookDeal();
    }

    /**
     * Get bookDealImages
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getBookDealImages()
    {
        return $this->bookDealImages;
    }

    /**
     * Set book
     *
     * @param \AppBundle\Entity\Book $book
     * @return BookDeal
     */
    public function setBook(\AppBundle\Entity\Book $book = null)
    {
        $this->book = $book;

        return $this;
    }

    /**
     * Get book
     *
     * @return \AppBundle\Entity\Book 
     */
    public function getBook()
    {
        return $this->book;
    }

    /**
     * Set seller
     *
     * @param \AppBundle\Entity\User $seller
     * @return BookDeal
     */
    public function setSeller(\AppBundle\Entity\User $seller = null)
    {
        $this->seller = $seller;

        return $this;
    }

    /**
     * Get seller
     *
     * @return \AppBundle\Entity\User 
     */
    public function getSeller()
    {
        return $this->seller;
    }

    /**
     * Set buyer
     *
     * @param \AppBundle\Entity\User $buyer
     * @return BookDeal
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
     * Set bookSubmittedDateTime
     *
     * @param \DateTime $bookSubmittedDateTime
     * @return BookDeal
     */
    public function setBookSubmittedDateTime($bookSubmittedDateTime)
    {
        $this->bookSubmittedDateTime = $bookSubmittedDateTime;

        return $this;
    }

    /**
     * Get bookSubmittedDateTime
     *
     * @return \DateTime 
     */
    public function getBookSubmittedDateTime()
    {
        return $this->bookSubmittedDateTime;
    }

    /**
     * Set bookPaymentMethodCashOnExchange
     *
     * @param boolean $bookPaymentMethodCashOnExchange
     * @return BookDeal
     */
    public function setBookPaymentMethodCashOnExchange($bookPaymentMethodCashOnExchange)
    {
        $this->bookPaymentMethodCashOnExchange = $bookPaymentMethodCashOnExchange;

        return $this;
    }

    /**
     * Get bookPaymentMethodCashOnExchange
     *
     * @return boolean 
     */
    public function getBookPaymentMethodCashOnExchange()
    {
        return $this->bookPaymentMethodCashOnExchange;
    }
}
