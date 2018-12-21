<?php

namespace AppBundle\OAuth;

use FOS\OAuthServerBundle\Storage\GrantExtensionInterface;
use OAuth2\Model\IOAuth2Client;
use Doctrine\Common\Persistence\ObjectRepository;

class SocialPluginGrantExtension implements GrantExtensionInterface {

    private $userRepository;

    public function __construct(ObjectRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /*
     * {@inheritdoc}
     */
    public function checkGrantExtension(IOAuth2Client $client, array $inputData, array $authHeaders)
    {


        $user = $this->userRepository->findOneBy(array(
            'googleId'=>$inputData['serviceId']
        ));

        if(!$user){
            $user = $this->userRepository->findOneBy(array(
                'facebookId'=>$inputData['serviceId']
            ));
        }
        if($user){
            return array(
                'data' => $user
            );

            return true;
        }

        return false;

    }
}