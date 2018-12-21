<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NewsImage
 */
class NewsImage
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $newsImageUrl;

    private $news;
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
     * Set newsImageUrl
     *
     * @param string $newsImageUrl
     * @return NewsImage
     */
    public function setNewsImageUrl($newsImageUrl)
    {
        $this->newsImageUrl = $newsImageUrl;

        return $this;
    }

    /**
     * Get newsImageUrl
     *
     * @return string 
     */
    public function getNewsImageUrl()
    {
        return $this->newsImageUrl;
    }

    /**
     * Set news
     *
     * @param \AppBundle\Entity\News $news
     * @return NewsImage
     */
    public function setNews(\AppBundle\Entity\News $news = null)
    {
        $this->news = $news;

        return $this;
    }

    /**
     * Get news
     *
     * @return \AppBundle\Entity\News 
     */
    public function getNews()
    {
        return $this->news;
    }
}
