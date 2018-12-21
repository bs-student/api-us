<?php

namespace AppBundle\Provider;

use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\FOSUBUserProvider as BaseClass;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserChecker;
use Symfony\Component\Security\Core\User\UserInterface;

class OAuthUserProvider extends BaseClass
{


    public function loadUserByOAuthUserResponse(UserResponseInterface $response) {


        $userId = $response->getUsername();
        $user = $this->userManager->findUserBy(array($this->getProperty($response) => $userId));

        $email = $response->getEmail();
        $username = $response->getNickname() ?: $response->getRealName();
        if (null === $user) {
            $user = $this->userManager->findUserByUsernameAndEmail($username, $email);

            if (null === $user || !$user instanceof UserInterface) {
                $user = $this->userManager->createUser();
                $username = str_replace(' ', '', $username.rand(20,50000));
                $user->setUsername($username);


                if($email==null){
                    $user->setEmail($userId);

                }else{
                    $user->setEmail($email);

                }
                $user->addRole('ROLE_NORMAL_USER');
                $user->setPassword('');
                $user->setEnabled(true);
                $user->setRegistrationStatus('incomplete');
                $user->setFullName($response->getRealName());
                //$user->setOAuthService($response->getResourceOwner()->getName());
                if($response->getResourceOwner()->getName()=="google"){
                    $user->setGoogleEmail($response->getEmail());
                    $user->setGoogleId($userId);
                    $user->setGoogleToken($response->getAccessToken());
                }
                if($response->getResourceOwner()->getName()=="facebook"){
                    if($email!=null)$user->setFacebookEmail($response->getEmail());
                    $user->setFacebookId($userId);
                    $user->setFacebookToken($response->getAccessToken());
                }


                $this->userManager->updateUser($user);
            } else {


                if($response->getResourceOwner()->getName()=="google"){
                    $user->setGoogleId($userId);
                    $user->setGoogleToken($response->getAccessToken());
                    $user->setGoogleEmail($response->getEmail());
                }
                if($response->getResourceOwner()->getName()=="facebook"){
                    if($email!=null)$user->setFacebookEmail($response->getEmail());
                    $user->setFacebookId($userId);
                    $user->setFacebookToken($response->getAccessToken());
                }



                $this->userManager->updateUser($user);
//                throw new AuthenticationException('Username or email has been already used.');
            }
        } else {
            $checker = new UserChecker();
            $checker->checkPreAuth($user);
        }
        return $user;

    }



}
