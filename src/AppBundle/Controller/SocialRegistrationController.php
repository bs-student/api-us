<?php
/**
 * Created by PhpStorm.
 * User: Sujit
 * Date: 1/14/16
 * Time: 1:45 PM
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Log;
use AppBundle\Entity\User;
use AppBundle\Form\Type\LogType;
use AppBundle\Form\Type\RegistrationType;
use AppBundle\Form\Type\SocialRegistrationType;
use AppBundle\Form\Type\UserType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\Security\Core\SecurityContext;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class SocialRegistrationController extends Controller
{


    /**
     * Check for Google User
     */
    public function authGoogleAction(Request $request)
    {
        $requestJson = $request->getContent();
        $requestData = json_decode($requestJson, true);

        $params = array(

            'code' => $requestData['code'],
            'client_id' => $requestData['clientId'],
            'client_secret' => $this->container->getParameter('google_app_info')['client_secret'],
            'redirect_uri' => $requestData['redirectUri'],
            'grant_type' => 'authorization_code'

        );

        // Step 1. Exchange authorization code for access token.

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,"https://accounts.google.com/o/oauth2/token");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $accessTokenResponse = curl_exec($ch);
        curl_close($ch);

        $accessToken = json_decode($accessTokenResponse, true);

        // Step 2. Retrieve profile information about the current user.

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,"https://www.googleapis.com/plus/v1/people/me?access_token=".$accessToken['access_token']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $profileResponse = curl_exec($ch);
        curl_close($ch);

        $profile = json_decode($profileResponse, true);

        // Step 3a. If user is already signed in then link accounts.

        $em = $this->getDoctrine()->getManager();
        $userRepo = $em->getRepository('AppBundle:User');
        $user = $userRepo->findOneBy(array('email'=>$profile['emails'][0]['value']));

        //Check if user found
        if($user instanceof User){

            //If User doesn't have Google Data
            if($user->getGoogleId()==null){

                //Update Data & Login

                $userForm = $this->createForm(new SocialRegistrationType(), $user);
                $userForm->remove('fullName');
                $userForm->remove('username');
                $userForm->remove('email');
                $userForm->remove('adminApproved');
                $userForm->remove('adminVerified');
                $userForm->remove('registrationStatus');
                $userForm->remove('referral');
                $userForm->remove('campus');
                $userForm->remove('facebookId');
                $userForm->remove('facebookEmail');
                $userForm->remove('facebookToken');

                $userForm->remove('emailNotification');
                $userForm->remove('registrationDateTime');

                $data=array();
                //If default Picture is found
                if(!strcmp($user->getProfilePicture(),"/userImages/default_profile_picture.jpg")){

                    //Save image from google plus

                    $fileDirHost = $this->container->getParameter('kernel.root_dir');
                    $fileDir = '/../web/userImages/';
                    $fileNameDir = '/userImages/';


                    if (strpos($profile['image']['url'], '?sz=') !== false) {
                        $profile['image']['url'] = substr($profile['image']['url'],0,strpos($profile['image']['url'], '?sz='))."?sz=200";
                    }else{
                        $profile['image']['url']  = $profile['image']['url']."?sz=200";
                    }

                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $profile['image']['url']);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                    $imageOutput   = curl_exec($ch);
                    curl_close($ch);

                    $fileSaveName = gmdate("Y-d-m_h_i_s_") . rand(0, 99999999) . ".jpg";
                    $fp = fopen($fileDirHost . $fileDir . $fileSaveName, 'x');
                    fwrite($fp, $imageOutput);
                    fclose($fp);
                    $this->_resize(200,200,$fileDirHost.$fileDir.$fileSaveName,$fileDirHost.$fileDir.$fileSaveName);

                    $data['profilePicture']= $fileNameDir . $fileSaveName;


                }else{
                    $userForm->remove('profilePicture');
                }


                $data['googleId'] =$profile['id'];
                $data['googleEmail'] =$profile['emails'][0]['value'];
                $data['googleToken'] = $accessToken['access_token'];
                $data['emailVerified'] = "Yes";

                $userForm->submit($data);

                if ($userForm->isValid()) {

                    $em->persist($user);
                    $em->flush();
                    if($user->getAdminApproved()==="No"){
                        return $this->_createJsonResponse('error',array(
                                'errorTitle'=>"Account is Blocked by Admin",
                                'errorDescription'=>"Your account is blocked by the admin. Please contact support or try with another Google account.",
                            )
                            ,400);
                    }elseif ($user->getAdminApproved()==="Yes") {
                        $logData = array(
                            'user' => $user->getId(),
                            'logType' => "Login",
                            'logDateTime' => gmdate('Y-m-d H:i:s'),
                            'logDescription' => $user->getUsername() . " has Logged In via Google",
                            'userIpAddress' => $this->container->get('request')->getClientIp(),
                            'logUserType' => in_array("ROLE_ADMIN_USER", $user->getRoles()) ? "Admin User" : "Normal User"
                        );
                        $this->_saveLog($logData);

                        return $this->_createJsonResponse('success', array(
                                'successTitle' => "You account has been merged with Google Account.",
                                'successData' => array(
                                    'username' => $user->getUsername(),
                                    'fullName' => $user->getFullName(),
                                    'email' => $user->getEmail(),
                                    'registrationStatus' => $user->getRegistrationStatus(),
                                    'serviceId' => $user->getGoogleId(),
                                    'service' => 'google'
                                ))
                            , 200);
                    }
                }else{
                    return $this->_createJsonResponse('error',array(
                            'errorTitle'=>"Sorry couldn't merge your data to existed user with mail ".$profile['email'],
                            'errorDescription'=>"Please Try Again Later",
                            'errorData'=>$userForm)
                        ,400);
                }
            }else{
                if($user->getAdminApproved()==="No"){
                    return $this->_createJsonResponse('error',array(
                            'errorTitle'=>"Account is Blocked by Admin",
                            'errorDescription'=>"Your account is blocked by the admin. Please contact support or try with another Google account.",
                        )
                        ,400);
                }elseif ($user->getAdminApproved()==="Yes") {
                    $logData = array(
                        'user' => $user->getId(),
                        'logType' => "Login",
                        'logDateTime' => gmdate('Y-m-d H:i:s'),
                        'logDescription' => $user->getUsername() . " has Logged In via Google",
                        'userIpAddress' => $this->container->get('request')->getClientIp(),
                        'logUserType' => in_array("ROLE_ADMIN_USER", $user->getRoles()) ? "Admin User" : "Normal User"
                    );
                    $this->_saveLog($logData);
                    // Google Data is merged so Return Data to Login
                    return $this->_createJsonResponse('success', array(
                            'successData' => array(
                                'username' => $user->getUsername(),
                                'fullName' => $user->getFullName(),
                                'email' => $user->getEmail(),
                                'registrationStatus' => $user->getRegistrationStatus(),
                                'serviceId' => $user->getGoogleId(),
                                'service' => 'google'
                            ))
                        , 200);
                }
            }




        }else{
            //Register
            $userEntity = new User();

            $userEntity->addRole('ROLE_NORMAL_USER');
            $userEntity->setPassword('');
            $userEntity->setEnabled(true);

            $userForm = $this->createForm(new SocialRegistrationType(), $userEntity);
            $userForm->remove('referral');
            $userForm->remove('campus');
            $userForm->remove('facebookId');
            $userForm->remove('facebookEmail');
            $userForm->remove('facebookToken');

            //Save image from google plus

            $fileDirHost = $this->container->getParameter('kernel.root_dir');
            $fileDir = '/../web/userImages/';
            $fileNameDir = '/userImages/';


            if (strpos($profile['image']['url'], '?sz=') !== false) {
                $profile['image']['url'] = substr($profile['image']['url'],0,strpos($profile['image']['url'], '?sz='))."?sz=200";
            }else{
                $profile['image']['url']  = $profile['image']['url']."?sz=200";
            }

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $profile['image']['url']);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            $imageOutput   = curl_exec($ch);
            curl_close($ch);

            $fileSaveName = gmdate("Y-d-m_h_i_s_") . rand(0, 99999999) . ".jpg";
            $fp = fopen($fileDirHost . $fileDir . $fileSaveName, 'x');
            fwrite($fp, $imageOutput);
            fclose($fp);
            $this->_resize(200,200,$fileDirHost.$fileDir.$fileSaveName,$fileDirHost.$fileDir.$fileSaveName);


            $data=array(
                'email'=>  $profile['emails'][0]['value'],
                'username'=>  $profile['name']['givenName'].$profile['name']['familyName'].intval(rand(1,9999999999)),
                'fullName' => $profile['displayName'],
                'googleId' =>$profile['id'],
                'googleEmail' =>$profile['emails'][0]['value'],
                'googleToken' => $accessToken['access_token'],
                'adminApproved' =>"Yes",
                'emailVerified' =>"Yes",
                'adminVerified' =>"No",
                'registrationStatus'=>"incomplete",
                'profilePicture'=>$fileNameDir . $fileSaveName,
                'emailNotification'=>"On",
                'registrationDateTime'=>gmdate('Y-m-d H:i:s')
            );

            $userForm->submit($data);

            if ($userForm->isValid()) {

                $em->persist($userEntity);
                $em->flush();

                $logData = array(
                    'user'=>$userEntity->getId(),
                    'logType'=>"Registration",
                    'logDateTime'=>gmdate('Y-m-d H:i:s'),
                    'logDescription'=> $userEntity->getUsername()." has Registered via Google",
                    'userIpAddress'=>$this->container->get('request')->getClientIp(),
                    'logUserType'=> in_array("ROLE_ADMIN_USER",$userEntity->getRoles())?"Admin User":"Normal User"
                );
                $this->_saveLog($logData);

                return $this->_createJsonResponse('success',array(
                        'successTitle'=>"You have been registered.",
                        'successDescription'=>"Please Fill Up the Next form to complete registration Process",
                        'successData'=>array(
                            'username'=>$userEntity->getUsername(),
                            'fullName'=>$userEntity->getFullName(),
                            'email'=>$userEntity->getEmail(),
                            'registrationStatus'=>$userEntity->getRegistrationStatus(),
                            'serviceId'=>$userEntity->getGoogleId(),
                            'service'=>'google'
                        ))
                    ,200);
            }else{
                return $this->_createJsonResponse('error',array(
                    'errorTitle'=>"Sorry we couldn't register you",
                    'errorDescription'=>"Please Try Again Later",
                    'errorData'=>$userForm)
                    ,400);
            }

        }


    }

    /**
     * Check for Facebook User
     */
    public function authFacebookAction(Request $request)
    {
        $requestJson = $request->getContent();
        $requestData = json_decode($requestJson, true);

        // Step 1. Exchange authorization code for access token.

        $params=array(
            'code' => $requestData['code'],
            'client_id' => $requestData['clientId'],
            'client_secret' => $this->container->getParameter('facebook_app_info')['client_secret'],
            'redirect_uri' => $requestData['redirectUri']
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,"https://graph.facebook.com/v2.5/oauth/access_token");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $accessTokenResponse = curl_exec($ch);
        curl_close($ch);

        $accessToken = json_decode($accessTokenResponse, true);



        // Step 2. Retrieve profile information about the current user.

        $fields = 'id,email,first_name,last_name,link,name,picture.type(large)';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,"https://graph.facebook.com/v2.5/me?access_token=".$accessToken['access_token']."&fields=".$fields);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $profileResponse = curl_exec($ch);
        curl_close($ch);

        $profile = json_decode($profileResponse,true);
        // Step 3a. If user is already signed in then link accounts.
        $em = $this->getDoctrine()->getManager();
        $userRepo = $em->getRepository('AppBundle:User');
        if(array_key_exists('email',$profile)){
            $user = $userRepo->findOneBy(array('email'=>$profile['email']));
            $email = $profile['email'];
            $emailNeeded = false;
        }else{
            $user = $userRepo->findOneBy(array('facebookId'=>$profile['id']));
            $email = $profile['id']."@facebook.com";
            $emailNeeded = true;
        }


        //Check if user found
        if($user instanceof User){

            //If User doesn't have Google Data
            if($user->getFacebookId()==null){

                //Update Data & Login

                $userForm = $this->createForm(new SocialRegistrationType(), $user);
                $userForm->remove('fullName');
                $userForm->remove('username');
                $userForm->remove('email');
                $userForm->remove('adminApproved');
                $userForm->remove('adminVerified');
                $userForm->remove('registrationStatus');
                $userForm->remove('referral');
                $userForm->remove('campus');
                $userForm->remove('googleId');
                $userForm->remove('googleEmail');
                $userForm->remove('googleToken');

                $userForm->remove('emailNotification');
                $userForm->remove('registrationDateTime');

                $data=array();
                //If default Picture is found
                if(!strcmp($user->getProfilePicture(),"/userImages/default_profile_picture.jpg")){

                    //Save image from facebook

                    $fileDirHost = $this->container->getParameter('kernel.root_dir');
                    $fileDir = '/../web/userImages/';
                    $fileNameDir = '/userImages/';

                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $profile['picture']['data']['url']);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                    $imageOutput   = curl_exec($ch);
                    curl_close($ch);

                    $fileSaveName = gmdate("Y-d-m_h_i_s_") . rand(0, 99999999) . ".jpg";
                    $fp = fopen($fileDirHost . $fileDir . $fileSaveName, 'x');
                    fwrite($fp, $imageOutput);
                    fclose($fp);
                    $this->_resize(200,200,$fileDirHost.$fileDir.$fileSaveName,$fileDirHost.$fileDir.$fileSaveName);

                    $data['profilePicture']= $fileNameDir . $fileSaveName;

                }else{
                    $userForm->remove('profilePicture');
                }

                $data['facebookId'] =$profile['id'];
                $data['facebookEmail'] =$email;
                $data['facebookToken'] = $accessToken['access_token'];
                $data['emailVerified'] = "Yes";

                $userForm->submit($data);

                if ($userForm->isValid()) {
                    $em->persist($user);
                    $em->flush();
                    if($user->getAdminApproved()==="No"){
                        return $this->_createJsonResponse('error',array(
                                'errorTitle'=>"Account is Blocked by Admin",
                                'errorDescription'=>"Your account is blocked by the admin. Please contact support or try with another account.",
                            )
                            ,400);
                    }elseif ($user->getAdminApproved()==="Yes") {
                        $logData = array(
                            'user' => $user->getId(),
                            'logType' => "Login",
                            'logDateTime' => gmdate('Y-m-d H:i:s'),
                            'logDescription' => $user->getUsername() . " has Logged In via Facebook",
                            'userIpAddress' => $this->container->get('request')->getClientIp(),
                            'logUserType' => in_array("ROLE_ADMIN_USER", $user->getRoles()) ? "Admin User" : "Normal User"
                        );
                        $this->_saveLog($logData);


                        return $this->_createJsonResponse('success', array(
                                'successTitle' => "You account has been merged with Facebook Account.",
                                'successData' => array(
                                    'username' => $user->getUsername(),
                                    'fullName' => $user->getFullName(),
                                    'email' => $user->getEmail(),
                                    'registrationStatus' => $user->getRegistrationStatus(),
                                    'serviceId' => $user->getFacebookId(),
                                    'emailNeeded' => $emailNeeded,
                                    'service' => 'facebook'
                                ))
                            , 200);
                    }
                }else{
                    return $this->_createJsonResponse('error',array(
                            'errorTitle'=>"Sorry couldn't merge your data to existed user with mail ".$profile['email'],
                            'errorDescription'=>"Please Try Again Later",
                            'errorData'=>$userForm)
                        ,400);
                }
            }else{
                if($user->getAdminApproved()==="No"){
                    return $this->_createJsonResponse('error',array(
                            'errorTitle'=>"Account is Blocked by Admin",
                            'errorDescription'=>"Your account is blocked by the admin. Please contact support or try with another account.",
                        )
                        ,400);
                }elseif ($user->getAdminApproved()==="Yes") {
                    $logData = array(
                        'user' => $user->getId(),
                        'logType' => "Login",
                        'logDateTime' => gmdate('Y-m-d H:i:s'),
                        'logDescription' => $user->getUsername() . " has Logged In via Facebook",
                        'userIpAddress' => $this->container->get('request')->getClientIp(),
                        'logUserType' => in_array("ROLE_ADMIN_USER", $user->getRoles()) ? "Admin User" : "Normal User"
                    );
                    $this->_saveLog($logData);
                    // Google Data is merged so Return Data to Login
                    return $this->_createJsonResponse('success', array(
                            'successData' => array(
                                'username' => $user->getUsername(),
                                'fullName' => $user->getFullName(),
                                'email' => $user->getEmail(),
                                'registrationStatus' => $user->getRegistrationStatus(),
                                'serviceId' => $user->getFacebookId(),
                                'emailNeeded' => $emailNeeded,
                                'service' => 'facebook'
                            ))
                        , 200);
                }
            }




        }else{
            //Register
            $userEntity = new User();

            $userEntity->addRole('ROLE_NORMAL_USER');
            $userEntity->setPassword('');
            $userEntity->setEnabled(true);

            $userForm = $this->createForm(new SocialRegistrationType(), $userEntity);
            $userForm->remove('referral');
            $userForm->remove('campus');
            $userForm->remove('googleId');
            $userForm->remove('googleEmail');
            $userForm->remove('googleToken');

            //Save image from facebook

            $fileDirHost = $this->container->getParameter('kernel.root_dir');
            $fileDir = '/../web/userImages/';
            $fileNameDir = '/userImages/';

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $profile['picture']['data']['url']);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            $imageOutput   = curl_exec($ch);
            curl_close($ch);

            $fileSaveName = gmdate("Y-d-m_h_i_s_") . rand(0, 99999999) . ".jpg";
            $fp = fopen($fileDirHost . $fileDir . $fileSaveName, 'x');
            fwrite($fp, $imageOutput);
            fclose($fp);
            $this->_resize(200,200,$fileDirHost.$fileDir.$fileSaveName,$fileDirHost.$fileDir.$fileSaveName);

            $data=array(
                'email'=>  $email,
                'username'=>  $profile['first_name'].$profile['last_name'].intval(rand(1,9999999999)),
                'fullName' => $profile['name'],
                'facebookId' =>$profile['id'],
                'facebookEmail' =>$email,
                'facebookToken' => $accessToken['access_token'],
                'adminApproved' =>"Yes",
                'emailVerified' =>"Yes",
                'adminVerified' =>"No",
                'registrationStatus'=>"incomplete",
                'profilePicture'=>$fileNameDir . $fileSaveName,
                'emailNotification'=>"On",
                'registrationDateTime'=>gmdate('Y-m-d H:i:s')
            );

            $userForm->submit($data);

            if ($userForm->isValid()) {

                $em->persist($userEntity);
                $em->flush();

                $logData = array(
                    'user'=>$userEntity->getId(),
                    'logType'=>"Registration",
                    'logDateTime'=>gmdate('Y-m-d H:i:s'),
                    'logDescription'=> $userEntity->getUsername()." has Registered via Facebook",
                    'userIpAddress'=>$this->container->get('request')->getClientIp(),
                    'logUserType'=> in_array("ROLE_ADMIN_USER",$userEntity->getRoles())?"Admin User":"Normal User"
                );
                $this->_saveLog($logData);

                return $this->_createJsonResponse('success',array(
                        'successTitle'=>"You have been registered.",
                        'successDescription'=>"Please Fill Up the Next form to complete registration Process",
                        'successData'=>array(
                            'username'=>$userEntity->getUsername(),
                            'fullName'=>$userEntity->getFullName(),
                            'email'=>$userEntity->getEmail(),
                            'registrationStatus'=>$userEntity->getRegistrationStatus(),
                            'serviceId'=>$userEntity->getFacebookId(),
                            'emailNeeded'=>$emailNeeded,
                            'service'=>'facebook'
                        ))
                    ,200);
            }else{
                return $this->_createJsonResponse('error',array(
                        'errorTitle'=>"Sorry we couldn't register you",
                        'errorDescription'=>"Please Try Again Later",
                        'errorData'=>$userForm)
                    ,400);
            }

        }
    }

    /**
     * Update Social User.
     *
     */
    public function updateSocialUserAction(Request $request)
    {
        $requestJson = $request->getContent();
        $requestData = json_decode($requestJson, true);

        $em = $this->getDoctrine()->getManager();
        $userRepo = $em->getRepository('AppBundle:User');

        $service = $requestData['user']['service'];
        $user = $userRepo->findOneBy(array($service."Id"=>$requestData['user']['serviceId']));

        if($user instanceof User){

            if($userRepo->checkIfUsernameExistByUsername($requestData['user']['username'], $user->getUsername())){
                return $this->_createJsonResponse('error',array(
                    'errorTitle'=>"Username Already Exist",
                    'errorDescription'=>"Please provide different username"
                ),400);
            }

            if($userRepo->checkIfEmailExistByEmail($requestData['user']['email'], $user->getEmail())){
                return $this->_createJsonResponse('error',array(
                    'errorTitle'=>"Email Already Exist",
                    'errorDescription'=>"Please provide different email"
                ),400);
            }

            $userForm = $this->createForm(new SocialRegistrationType(), $user);
            $userForm->remove('fullName');
            $userForm->remove('adminApproved');
            $userForm->remove('emailVerified');
            $userForm->remove('adminVerified');
            $userForm->remove('googleId');
            $userForm->remove('googleEmail');
            $userForm->remove('googleToken');
            $userForm->remove('facebookId');
            $userForm->remove('facebookEmail');
            $userForm->remove('facebookToken');
            $userForm->remove('profilePicture');
            $userForm->remove('emailNotification');

            $data=array(
                'registrationStatus'=>"complete",
                'referral'=>$requestData['user']['referral'],
                'campus'=>$requestData['user']['campus'],
                'username'=>$requestData['user']['username'],
                'email'=>$requestData['user']['email'],
                'registrationDateTime'=>gmdate('Y-m-d H:i:s')
            );

            $userForm->submit($data);
            if($userForm->isValid()){
                $em->persist($user);
                $em->flush();
                return $this->_createJsonResponse('success',array(
                        'successTitle'=>"Your Registration is Completed",
                        'successData'=>array(
                            'username'=>$user->getUsername(),
                            'fullName'=>$user->getFullName(),
                            'email'=>$user->getEmail(),
                            'registrationStatus'=>$user->getRegistrationStatus(),
                            'serviceId'=>$requestData['user']['service']=='google'?$user->getGoogleId():$user->getFacebookId(),
                            'service'=>$requestData['user']['service']
                        ))
                    ,200);
            }else{
                return $this->_createJsonResponse('error',array(
                        'errorTitle'=>"Sorry registration couldn't be completed",
                        'errorDescription'=>"Please Try Again Later",
                        'errorData'=>$userForm)
                    ,400);
            }

        }else{
            return $this->_createJsonResponse('error',array(
                'errorTitle'=>"Sorry User was not found"
            ),400);
        }



    }

    // New Image  Resize function
    public function _resize($newWidth , $newHeight, $targetFile, $originalFile) {

        $info = getimagesize($originalFile);
        $mime = $info['mime'];

        switch ($mime) {
            case 'image/jpeg':
                $image_create_func = 'imagecreatefromjpeg';
                $image_save_func = 'imagejpeg';
//                $new_image_ext = 'jpg';
                break;

            case 'image/png':
                $image_create_func = 'imagecreatefrompng';
                $image_save_func = 'imagepng';
//                $new_image_ext = 'png';
                break;

            case 'image/gif':
                $image_create_func = 'imagecreatefromgif';
                $image_save_func = 'imagegif';
//                $new_image_ext = 'gif';
                break;

            default:
                throw new Exception('Unknown image type.');
        }

        $img = $image_create_func($originalFile);
        list($width, $height) = getimagesize($originalFile);

//        $newHeight = ($height / $width) * $newWidth;
        $tmp = imagecreatetruecolor($newWidth, $newHeight);
        imagecopyresampled($tmp, $img, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        if (file_exists($targetFile)) {
            unlink($targetFile);
        }
        $image_save_func($tmp, "$targetFile");
    }


    public function _saveLog($logData){
        $em = $this->container->get('doctrine')->getManager();
        $log = new Log();
        $logForm = $this->container->get('form.factory')->create(new LogType(), $log);

        $logForm->submit($logData);
        if($logForm->isValid()){
            $em->persist($log);
            $em->flush();
        }
    }

    public function _createJsonResponse($key, $data, $code)
    {
        $serializer = $this->container->get('jms_serializer');
        $json = $serializer->serialize([$key => $data], 'json');
        $response = new Response($json, $code);
        return $response;
    }
} 