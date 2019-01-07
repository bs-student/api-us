<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\Log;
use AppBundle\Form\Type\LogType;
use AppBundle\Validator\Constraints\UsernameConstraints;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\Type\UserType;
use AppBundle\Entity\User;
use Symfony\Component\HttpKernel\Exception\HttpException;
use FOS\RestBundle\Util\Codes;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\Extension\Validator\ViolationMapper\ViolationMapper;
use Symfony\Component\Validator\ConstraintViolation;


class AdminUserApiController extends Controller
{

    /**
     * Get all Non Approved users
     *
     */
    public function getAllNonApprovedUserAction(Request $request){

        $user = $this->container->get('security.token_storage')->getToken()->getUser();

        if(in_array('ROLE_ADMIN_USER',$user->getRoles(),true)){

            $content = $request->getContent();
            $data = json_decode($content, true);
            $em = $this->getDoctrine()->getManager();
            $userRepo=$em->getRepository('AppBundle:User');


            $pageSize = $data["pageSize"];
            $usernameQuery = filter_var($data["usernameQuery"], FILTER_SANITIZE_STRING);
            $emailQuery = filter_var($data["emailQuery"], FILTER_SANITIZE_STRING);
            $fullNameQuery = filter_var($data["fullNameQuery"], FILTER_SANITIZE_STRING);
            $emailVerifiedQuery = $data["emailVerifiedQuery"];
            $typeQuery = $data["typeQuery"];
            $pageNumber = $data["pageNumber"];
            $sort = $data["sort"];



            $totalNumber = $userRepo->getNonApprovedUserSearchNumber($usernameQuery,$emailQuery,$fullNameQuery,$emailVerifiedQuery,$typeQuery);
            $users = $userRepo->getNonApprovedUserSearchResult($usernameQuery,$emailQuery,$fullNameQuery,$emailVerifiedQuery, $pageNumber, $pageSize,$sort,$typeQuery);

            for($i=0;$i<count($users);$i++){
                $users[$i]['registrationDateTime'] =$users[$i]['registrationDateTime']->format('g:i A, d M Y');

                if($users[$i]['googleId']!=null && $users[$i]['facebookId']==null){
                    $users[$i]['userType']="Google User";
                }elseif($users[$i]['googleId']==null && $users[$i]['facebookId']!=null){
                    $users[$i]['userType']="Facebook User";
                }elseif($users[$i]['googleId']!=null && $users[$i]['facebookId']!=null){
                    $users[$i]['userType']="Google & Facebook User";
                }elseif($users[$i]['googleId']==null && $users[$i]['facebookId']==null){
                    $users[$i]['userType']="Normal User";
                }
            }


            $data = array(
                'totalUsers' => $users ,
                'totalNumber' => $totalNumber
            );

            return $this->_createJsonResponse('success', array('successData'=>array('users'=>$data)), 200);
        }else{
            return $this->_createJsonResponse('error', array('errorTitle'=>"You are not authorized to see this page."), 400);
        }


    }

    /**
     * Get all Approved users
     *
     */
    public function getAllApprovedUserAction(Request $request){

        $user = $this->container->get('security.token_storage')->getToken()->getUser();

        if(in_array('ROLE_ADMIN_USER',$user->getRoles(),true)){

            $content = $request->getContent();
            $data = json_decode($content, true);
            $em = $this->getDoctrine()->getManager();
            $userRepo=$em->getRepository('AppBundle:User');


            $pageSize = $data["pageSize"];
            $usernameQuery = filter_var($data["usernameQuery"], FILTER_SANITIZE_STRING);
            $emailQuery = filter_var($data["emailQuery"], FILTER_SANITIZE_STRING);
            $fullNameQuery = filter_var($data["fullNameQuery"], FILTER_SANITIZE_STRING);
            $universityNameQuery = filter_var($data["universityNameQuery"], FILTER_SANITIZE_STRING);
            $campusNameQuery = filter_var($data["campusNameQuery"], FILTER_SANITIZE_STRING);
            $typeQuery = $data["typeQuery"];
            $emailVerifiedQuery = $data["emailVerifiedQuery"];
            $adminApprovedQuery = $data["adminApprovedQuery"];
            $pageNumber = $data["pageNumber"];
            $sort = $data["sort"];



            $totalNumber = $userRepo->getApprovedUserSearchNumber($usernameQuery,$emailQuery,$fullNameQuery,$universityNameQuery,$campusNameQuery,$emailVerifiedQuery,$adminApprovedQuery,$typeQuery);
            $users = $userRepo->getApprovedUserSearchResult($usernameQuery,$emailQuery,$fullNameQuery,$universityNameQuery,$campusNameQuery,$emailVerifiedQuery,$adminApprovedQuery, $pageNumber, $pageSize,$sort,$typeQuery);

            for($i=0;$i<count($users);$i++){
                $users[$i]['registrationDateTime'] =$users[$i]['registrationDateTime']->format('g:i A, d M Y');
                if($users[$i]['googleId']!=null && $users[$i]['facebookId']==null){
                    $users[$i]['userType']="Google User";
                }elseif($users[$i]['googleId']==null && $users[$i]['facebookId']!=null){
                    $users[$i]['userType']="Facebook User";
                }elseif($users[$i]['googleId']!=null && $users[$i]['facebookId']!=null){
                    $users[$i]['userType']="Google & Facebook User";
                }elseif($users[$i]['googleId']==null && $users[$i]['facebookId']==null){
                    $users[$i]['userType']="Normal User";
                }
            }

            $data = array(
                'totalUsers' => $users ,
                'totalNumber' => $totalNumber
            );

            return $this->_createJsonResponse('success', array('successData'=>array('users'=>$data)), 200);
        }else{
            return $this->_createJsonResponse('error', array('errorTitle'=>"You are not authorized to see this page."), 400);
        }


    }

    /**
     * Get all Admin Users
     *
     */
    public function getAllAdminUserAction(Request $request){

        $user = $this->container->get('security.token_storage')->getToken()->getUser();

        if(in_array('ROLE_ADMIN_USER',$user->getRoles(),true)){

            $content = $request->getContent();
            $data = json_decode($content, true);
            $em = $this->getDoctrine()->getManager();
            $userRepo=$em->getRepository('AppBundle:User');


            $pageSize = $data["pageSize"];
            $usernameQuery = filter_var($data["usernameQuery"], FILTER_SANITIZE_STRING);
            $emailQuery = filter_var($data["emailQuery"], FILTER_SANITIZE_STRING);
            $pageNumber = $data["pageNumber"];
            $sort = $data["sort"];


            $totalNumber = $userRepo->getAdminUserSearchNumber($usernameQuery,$emailQuery);
            $users = $userRepo->getAdminUserSearchResult($usernameQuery,$emailQuery, $pageNumber, $pageSize,$sort);

            $data = array(
                'totalUsers' => $users ,
                'totalNumber' => $totalNumber
            );

            return $this->_createJsonResponse('success', array('successData'=>array('users'=>$data)), 200);
        }else{
            return $this->_createJsonResponse('error', array('errorTitle'=>"You are not authorized to see this page."), 400);
        }

    }

    /**
     * Admin Update User Data
     */
    public function adminUpdateUserDataAction(Request $request)
    {
        $user = $this->container->get('security.context')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();
        $userRepo = $em->getRepository('AppBundle:User');

        $request_data = json_decode($request->getContent(),true);

        if(in_array('ROLE_ADMIN_USER',$user->getRoles(),true)){
            $editedUser = $userRepo->findOneBy(array("id" => $request_data['userId']));

            if ($editedUser != null) {

                if ($userRepo->checkIfUsernameExistByUsername($request_data['username'], $editedUser->getUsername())) {

                    return $this->_createJsonResponse('error',array(
                        'errorTitle'=>"Can't Approve User",
                        'errorDescription'=> "Username '" . $request_data['username'] . "' Already Exist",
                        'errorData'=> array(
                            'username'=> $user->getUsername()
                        )
                    ),400);

                } else {

                    $userForm = $this->createForm(new UserType(), $editedUser);
                    $editedUser->setEnabled(true);
                    $userForm->remove('fullName');
                    $userForm->remove('email');
                    $userForm->remove('referral');
                    $userForm->remove('campus');
                    $userForm->remove('wishLists');
                    $userForm->remove('emailVerified');
                    $userForm->submit(array(
                        "adminVerified"=>$request_data['adminVerified'],
                        "adminApproved"=>$request_data['adminApproved'],
                        "username"=>$request_data['username'],
                    ));
                    if($userForm->isValid()){
                        $em->persist($editedUser);
                        $em->flush();

                        $action = $request_data['adminApproved']==="Yes"? "Approved":"Disapproved";
                        $logData = array(
                            'user'=>$user->getId(),
                            'logType'=>"Update User",
                            'logDateTime'=>gmdate('Y-m-d H:i:s'),
                            'logDescription'=> $user->getUsername()." has updated & ".$action." user named ".$editedUser->getUsername(),
                            'userIpAddress'=>$this->container->get('request')->getClientIp(),
                            'logUserType'=> in_array("ROLE_ADMIN_USER",$user->getRoles())?"Admin User":"Normal User"
                        );
                        $this->_saveLog($logData);

                        return $this->_createJsonResponse('success',array(
                            'successTitle'=>"User Updated",
                        ),200);
                    }else{
                        return $this->_createJsonResponse('error',array(
                            'errorTitle'=>"User not Updated",
                            'errorDescription'=>"Check the form and submit again",
                        ),200);
                    }

                }

            }
        }else{
            return $this->_createJsonResponse('error', array('errorTitle'=>"You are not authorized to see this page."), 400);
        }


    }

    /**
     * Approve Users
     */
    public function approveUsersAction(Request $request){
        $content = $request->getContent();
        $data = json_decode($content, true);
        $user = $this->container->get('security.context')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();
        $userRepo = $em->getRepository('AppBundle:User');


        if(in_array('ROLE_ADMIN_USER',$user->getRoles(),true)){

            if(count($data)>0){
                $userRepo->approveUsers($data);

                $users = "";
                foreach($data as $userRow){
                    $users.=$userRow['username'].", ";
                }

                $logData = array(
                    'user'=>$user->getId(),
                    'logType'=>"Approve User",
                    'logDateTime'=>gmdate('Y-m-d H:i:s'),
                    'logDescription'=> $user->getUsername()." has approved users named ".$users,
                    'userIpAddress'=>$this->container->get('request')->getClientIp(),
                    'logUserType'=> in_array("ROLE_ADMIN_USER",$user->getRoles())?"Admin User":"Normal User"
                );
                $this->_saveLog($logData);

                return $this->_createJsonResponse('success',array(
                    'successTitle'=>"Users been Approved",
                ),200);
            }else{
                return $this->_createJsonResponse('error', array('errorTitle'=>"No User was approved."), 400);
            }


        }else{
            return $this->_createJsonResponse('error', array('errorTitle'=>"You are not authorized to see this page."), 400);
        }


    }

    /**
     * Add Admin Users
     */
    public function addAdminUserAction(Request $request)
    {

        $user = $this->container->get('security.context')->getToken()->getUser();

        if(in_array('ROLE_ADMIN_USER',$user->getRoles(),true)){

            $formHandler = $this->container->get('fos_user.registration.form.handler');

            $form = $this->container->get('fos_user.registration.form');

            $form->remove('googleId');
            $form->remove('facebookId');
            $form->remove('googleEmail');
            $form->remove('facebookEmail');
            $form->remove('registrationStatus');
            $form->remove('registrationStatus');
            $form->remove('googleToken');
            $form->remove('facebookToken');
            $form->remove('campus');
            $form->remove('referral');
            $form->remove('googleToken');


            $confirmationEnabled = false;


            $process = $formHandler->process($confirmationEnabled);

            if ($process) {

                $em = $this->getDoctrine()->getManager();

                $addedUser = $form->getData();
                $addedUser->addRole("ROLE_ADMIN_USER");
                $addedUser->setAdminApproved("Yes");

                $em->persist($addedUser);
                $em->flush();

                $logData = array(
                    'user'=>$user->getId(),
                    'logType'=>"Add Admin User",
                    'logDateTime'=>gmdate('Y-m-d H:i:s'),
                    'logDescription'=> $user->getUsername()." has created another admin user named ".$addedUser->getUsername(),
                    'userIpAddress'=>$this->container->get('request')->getClientIp(),
                    'logUserType'=> in_array("ROLE_ADMIN_USER",$user->getRoles())?"Admin User":"Normal User"
                );
                $this->_saveLog($logData);

                $addedUserData=array(
                    'email'=>$addedUser->getEmail(),
                    'enabled'=>$addedUser->isEnabled(),
                    'fullName'=>$addedUser->getFullName(),
                    'roles'=>$addedUser->getRoles(),
                    'userId'=>$addedUser->getId(),
                    'username'=>$addedUser->getUsername(),
                    'profilePicture'=>$addedUser->getProfilePicture()
                );

                return $this->_createJsonResponse('success', array(
                    'successTitle'=>"Admin User Added",
                    'successData'=>$addedUserData
                ), 201);


            }else{
                return $this->_createJsonResponse('error', array(
                    'errorTitle' => "Admin User Couldn't be created",
                    'errorDescription' => "Sorry we were unable to add admin user. Reload the page and try again.",
                    'errorData' => $form
                ), 400);
            }


        }else{
            return $this->_createJsonResponse('error', array('errorTitle'=>"You are not authorized to see this page."), 400);
        }

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

    public function _createJsonResponse($key, $data,$code)
    {
        $serializer = $this->container->get('jms_serializer');
        $json = $serializer->serialize([$key => $data], 'json');
        $response = new Response($json, $code);
        return $response;
    }
}
