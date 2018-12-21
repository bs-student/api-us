<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Quote
 */
class Quote
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $quoteType;

    /**
     * @var string
     */
    private $quoteDescription;

    /**
     * @var string
     */
    private $quoteImage;

    /**
     * @var string
     */
    private $quoteProvider;


    /**
     * @var string
     */
    private $quoteStatus;

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
     * Set quoteType
     *
     * @param string $quoteType
     * @return Quote
     */
    public function setQuoteType($quoteType)
    {
        $this->quoteType = $quoteType;

        return $this;
    }

    /**
     * Get quoteType
     *
     * @return string 
     */
    public function getQuoteType()
    {
        return $this->quoteType;
    }

    /**
     * Set quoteDescription
     *
     * @param string $quoteDescription
     * @return Quote
     */
    public function setQuoteDescription($quoteDescription)
    {
        $this->quoteDescription = $quoteDescription;

        return $this;
    }

    /**
     * Get quoteDescription
     *
     * @return string 
     */
    public function getQuoteDescription()
    {
        return $this->quoteDescription;
    }

    /**
     * Set quoteImage
     *
     * @param string $quoteImage
     * @return Quote
     */
    public function setQuoteImage($quoteImage)
    {
        $this->quoteImage = $quoteImage;

        return $this;
    }

    /**
     * Get quoteImage
     *
     * @return string 
     */
    public function getQuoteImage()
    {
        return $this->quoteImage;
    }

    /**
     * Set quoteProvider
     *
     * @param string $quoteProvider
     * @return Quote
     */
    public function setQuoteProvider($quoteProvider)
    {
        $this->quoteProvider = $quoteProvider;

        return $this;
    }

    /**
     * Get quoteProvider
     *
     * @return string 
     */
    public function getQuoteProvider()
    {
        return $this->quoteProvider;
    }

    /**
     * Set quoteStatus
     *
     * @param string $quoteStatus
     * @return Quote
     */
    public function setQuoteStatus($quoteStatus)
    {
        $this->quoteStatus = $quoteStatus;

        return $this;
    }

    /**
     * Get quoteStatus
     *
     * @return string 
     */
    public function getQuoteStatus()
    {
        return $this->quoteStatus;
    }
}
