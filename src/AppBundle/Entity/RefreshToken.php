<?php


namespace AppBundle\Entity;

use FOS\OAuthServerBundle\Entity\RefreshToken as BaseRefreshToken;
use Doctrine\ORM\Mapping as ORM;

/**
 * RefreshToken
 */
class RefreshToken extends BaseRefreshToken
{
    /**
     * @var integer
     */
    protected $id;

    protected $client;
    protected $user;
}