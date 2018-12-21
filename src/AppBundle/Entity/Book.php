<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * Book
 */
class Book
{
    public function __construct()
    {
        $this->bookDeals = new ArrayCollection();

    }

    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $bookTitle;

    /**
     * @var string
     */
    private $bookDirectorAuthorArtist;

    /**
     * @var string
     */
    private $bookEdition;

    /**
     * @var string
     */
    private $bookIsbn10;

    /**
     * @var string
     */
    private $bookIsbn13;

    /**
     * @var string
     */
    private $bookPublisher;

    /**
     * @var \DateTime
     */
    private $bookPublishDate;

    /**
     * @var string
     */
    private $bookBinding;

    /**
     * @var string
     */
    private $bookPage;

    /**
     * @var string
     */
    private $bookLanguage;

    /**
     * @var string
     */
    private $bookDescription;

    /**
     * @var string
     */
    private $bookImage;
    /**
     * @var string
     */
    private $bookAmazonPrice;



    
    private $bookDeals;



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
     * Set bookTitle
     *
     * @param string $bookTitle
     * @return Book
     */
    public function setBookTitle($bookTitle)
    {
        $this->bookTitle = $bookTitle;

        return $this;
    }

    /**
     * Get bookTitle
     *
     * @return string 
     */
    public function getBookTitle()
    {
        return $this->bookTitle;
    }

    /**
     * Set bookDirectorAuthorArtist
     *
     * @param string $bookDirectorAuthorArtist
     * @return Book
     */
    public function setBookDirectorAuthorArtist($bookDirectorAuthorArtist)
    {
        $this->bookDirectorAuthorArtist = $bookDirectorAuthorArtist;

        return $this;
    }

    /**
     * Get bookDirectorAuthorArtist
     *
     * @return string 
     */
    public function getBookDirectorAuthorArtist()
    {
        return $this->bookDirectorAuthorArtist;
    }

    /**
     * Set bookEdition
     *
     * @param string $bookEdition
     * @return Book
     */
    public function setBookEdition($bookEdition)
    {
        $this->bookEdition = $bookEdition;

        return $this;
    }

    /**
     * Get bookEdition
     *
     * @return string 
     */
    public function getBookEdition()
    {
        return $this->bookEdition;
    }

    /**
     * Set bookIsbn10
     *
     * @param string $bookIsbn10
     * @return Book
     */
    public function setBookIsbn10($bookIsbn10)
    {
        $this->bookIsbn10 = $bookIsbn10;

        return $this;
    }

    /**
     * Get bookIsbn10
     *
     * @return string 
     */
    public function getBookIsbn10()
    {
        return $this->bookIsbn10;
    }

    /**
     * Set bookIsbn13
     *
     * @param string $bookIsbn13
     * @return Book
     */
    public function setBookIsbn13($bookIsbn13)
    {
        $this->bookIsbn13 = $bookIsbn13;

        return $this;
    }

    /**
     * Get bookIsbn13
     *
     * @return string 
     */
    public function getBookIsbn13()
    {
        return $this->bookIsbn13;
    }

    /**
     * Set bookPublisher
     *
     * @param string $bookPublisher
     * @return Book
     */
    public function setBookPublisher($bookPublisher)
    {
        $this->bookPublisher = $bookPublisher;

        return $this;
    }

    /**
     * Get bookPublisher
     *
     * @return string 
     */
    public function getBookPublisher()
    {
        return $this->bookPublisher;
    }

    /**
     * Set bookPublishDate
     *
     * @param \DateTime $bookPublishDate
     * @return Book
     */
    public function setBookPublishDate($bookPublishDate)
    {
        $this->bookPublishDate = $bookPublishDate;

        return $this;
    }

    /**
     * Get bookPublishDate
     *
     * @return \DateTime 
     */
    public function getBookPublishDate()
    {
        return $this->bookPublishDate;
    }

    /**
     * Set bookBinding
     *
     * @param string $bookBinding
     * @return Book
     */
    public function setBookBinding($bookBinding)
    {
        $this->bookBinding = $bookBinding;

        return $this;
    }

    /**
     * Get bookBinding
     *
     * @return string 
     */
    public function getBookBinding()
    {
        return $this->bookBinding;
    }

    /**
     * Set bookPage
     *
     * @param string $bookPage
     * @return Book
     */
    public function setBookPage($bookPage)
    {
        $this->bookPage = $bookPage;

        return $this;
    }

    /**
     * Get bookPage
     *
     * @return string 
     */
    public function getBookPage()
    {
        return $this->bookPage;
    }

    /**
     * Set bookLanguage
     *
     * @param string $bookLanguage
     * @return Book
     */
    public function setBookLanguage($bookLanguage)
    {
        $this->bookLanguage = $bookLanguage;

        return $this;
    }

    /**
     * Get bookLanguage
     *
     * @return string 
     */
    public function getBookLanguage()
    {
        return $this->bookLanguage;
    }

    /**
     * Set bookDescription
     *
     * @param string $bookDescription
     * @return Book
     */
    public function setBookDescription($bookDescription)
    {
        $this->bookDescription = $bookDescription;

        return $this;
    }

    /**
     * Get bookDescription
     *
     * @return string 
     */
    public function getBookDescription()
    {
        return $this->bookDescription;
    }

    /**
     * Set bookImage
     *
     * @param string $bookImage
     * @return Book
     */
    public function setBookImage($bookImage)
    {
        $this->bookImage = $bookImage;

        return $this;
    }

    /**
     * Get bookImage
     *
     * @return string 
     */
    public function getBookImage()
    {
        return $this->bookImage;
    }

    /**
     * Set bookAmazonPrice
     *
     * @param string $bookAmazonPrice
     * @return Book
     */
    public function setBookAmazonPrice($bookAmazonPrice)
    {
        $this->bookAmazonPrice = $bookAmazonPrice;

        return $this;
    }

    /**
     * Get bookAmazonPrice
     *
     * @return string 
     */
    public function getBookAmazonPrice()
    {
        return $this->bookAmazonPrice;
    }

    /**
     * Add bookDeals
     *
     * @param \AppBundle\Entity\BookDeal $bookDeals
     * @return Book
     */
    public function addBookDeal(\AppBundle\Entity\BookDeal $bookDeals)
    {
        $this->bookDeals[] = $bookDeals;

        return $this;
    }

    /**
     * Remove bookDeals
     *
     * @param \AppBundle\Entity\BookDeal $bookDeals
     */
    public function removeBookDeal(\AppBundle\Entity\BookDeal $bookDeals)
    {
        $this->bookDeals->removeElement($bookDeals);
    }

    /**
     * Get bookDeals
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getBookDeals()
    {
        return $this->bookDeals;
    }
}
