<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * WishList
 */
class WishList
{
    /**
     * @var integer
     */
    private $id;


    private $user;

    private $book;

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
     * @return WishList
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
     * Set book
     *
     * @param \AppBundle\Entity\Book $book
     * @return WishList
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
    public function __toString()
    {
        return strval($this->id);
    }

}
