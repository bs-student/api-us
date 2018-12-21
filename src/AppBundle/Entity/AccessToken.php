<?php


namespace AppBundle\Entity;

use FOS\OAuthServerBundle\Entity\AccessToken as BaseAccessToken;
use Doctrine\ORM\Mapping as ORM;

/**
 * AccessToken
 */
class AccessToken extends BaseAccessToken
{
    /**
     * @var integer
     */
    protected $id;

    protected $client;


    protected $user;
}