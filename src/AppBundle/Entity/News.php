<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * News
 */
class News
{
    public function __construct()
    {
        $this->newsImages = new ArrayCollection();
    }

    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $newsTitle;

    /**
     * @var string
     */
    private $newsDescription;

    /**
     * @var string
     */
    private $newsType;

    /**
     * @var string
     */
    private $newsVideoEmbedCode;

    /**
     * @var \DateTime
     */
    private $newsDateTime;

    /**
     * @var string
     */
    private $newsStatus;

    private $newsImages;

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
     * Set newsTitle
     *
     * @param string $newsTitle
     * @return News
     */
    public function setNewsTitle($newsTitle)
    {
        $this->newsTitle = $newsTitle;

        return $this;
    }

    /**
     * Get newsTitle
     *
     * @return string 
     */
    public function getNewsTitle()
    {
        return $this->newsTitle;
    }

    /**
     * Set newsDescription
     *
     * @param string $newsDescription
     * @return News
     */
    public function setNewsDescription($newsDescription)
    {
        $this->newsDescription = $newsDescription;

        return $this;
    }

    /**
     * Get newsDescription
     *
     * @return string 
     */
    public function getNewsDescription()
    {
        return $this->newsDescription;
    }

    /**
     * Set newsDateTime
     *
     * @param \DateTime $newsDateTime
     * @return News
     */
    public function setNewsDateTime($newsDateTime)
    {
        $this->newsDateTime = $newsDateTime;

        return $this;
    }

    /**
     * Get newsDateTime
     *
     * @return \DateTime 
     */
    public function getNewsDateTime()
    {
        return $this->newsDateTime;
    }

    /**
     * Set newsStatus
     *
     * @param string $newsStatus
     * @return News
     */
    public function setNewsStatus($newsStatus)
    {
        $this->newsStatus = $newsStatus;

        return $this;
    }

    /**
     * Get newsStatus
     *
     * @return string 
     */
    public function getNewsStatus()
    {
        return $this->newsStatus;
    }

    /**
     * Add newsImages
     *
     * @param \AppBundle\Entity\NewsImage $newsImages
     * @return News
     */
    public function addNewsImage(\AppBundle\Entity\NewsImage $newsImages)
    {
        $this->newsImages->add($newsImages);
        $newsImages->setNews($this);
        return $this;
    }

    /**
     * Remove newsImages
     *
     * @param \AppBundle\Entity\NewsImage $newsImages
     */
    public function removeNewsImage(\AppBundle\Entity\NewsImage $newsImages)
    {
        $this->newsImages->removeElement($newsImages);
    }

    /**
     * Get newsImages
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getNewsImages()
    {
        return $this->newsImages;
    }

    /**
     * Set newsType
     *
     * @param string $newsType
     * @return News
     */
    public function setNewsType($newsType)
    {
        $this->newsType = $newsType;

        return $this;
    }

    /**
     * Get newsType
     *
     * @return string 
     */
    public function getNewsType()
    {
        return $this->newsType;
    }

    /**
     * Set newsVideoLink
     *
     * @param string $newsVideoLink
     * @return News
     */
    public function setNewsVideoLink($newsVideoLink)
    {
        $this->newsVideoLink = $newsVideoLink;

        return $this;
    }

    /**
     * Get newsVideoLink
     *
     * @return string 
     */
    public function getNewsVideoLink()
    {
        return $this->newsVideoLink;
    }

    /**
     * Set newsVideoEmbedCode
     *
     * @param string $newsVideoEmbedCode
     * @return News
     */
    public function setNewsVideoEmbedCode($newsVideoEmbedCode)
    {
        $this->newsVideoEmbedCode = $newsVideoEmbedCode;

        return $this;
    }

    /**
     * Get newsVideoEmbedCode
     *
     * @return string 
     */
    public function getNewsVideoEmbedCode()
    {
        return $this->newsVideoEmbedCode;
    }

}
