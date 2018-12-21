<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Star
 */
class Star
{
    /**
     * @var integer
     */
    private $id;

    private $user;

    private $bookDeal;

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
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     * @return Star
     */
    public function setUser(\AppBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \AppBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set bookDeal
     *
     * @param \AppBundle\Entity\BookDeal $bookDeal
     * @return Star
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
}
