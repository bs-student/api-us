<?php
namespace AppBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * BookImage
 */
class BookDealImage
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
    private $imageUrl;


    private $bookDeal;


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
     * Set imageUrl
     *
     * @param string $imageUrl
     * @return BookDealImage
     */
    public function setImageUrl($imageUrl)
    {
        $this->imageUrl = $imageUrl;

        return $this;
    }

    /**
     * Get imageUrl
     *
     * @return string 
     */
    public function getImageUrl()
    {
        return $this->imageUrl;
    }

    /**
     * Set bookDeal
     *
     * @param \AppBundle\Entity\BookDeal $bookDeal
     * @return BookDealImage
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
     * Remove bookDeal
     *
     * @return BookDealImage
     */
    public function removeBookDeal()
    {
        return $this->setBookDeal(null);
    }
}
