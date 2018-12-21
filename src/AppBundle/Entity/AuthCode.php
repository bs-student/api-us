<?php


namespace AppBundle\Entity;

use FOS\OAuthServerBundle\Entity\AuthCode as BaseAuthCode;
use Doctrine\ORM\Mapping as ORM;

/**
 * AuthCode
 */
class AuthCode extends BaseAuthCode
{
    /**
     * @var integer
     */
    protected $id;

    protected $client;
    protected $user;
}