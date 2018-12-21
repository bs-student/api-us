<?php


namespace AppBundle\Entity;

use FOS\OAuthServerBundle\Entity\Client as BaseClient;
use Doctrine\ORM\Mapping as ORM;

/**
 * Client
 */
class Client extends BaseClient
{
    /**
     * @var integer
     */
    protected $id;

    public function __construct()
    {
        parent::__construct();
        // your own logic
    }
}