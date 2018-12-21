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


class AdminDashboardApiController extends Controller
{

    /**
     * Get all Normal and Social User data
     *
     */
    public function getAllNormalAndSocialUserDataAction(Request $request){

        $user = $this->container->get('security.token_storage')->getToken()->getUser();

        if(in_array('ROLE_ADMIN_USER',$user->getRoles(),true)){

            $content = $request->getContent();
            $data = json_decode($content, true);
            $em = $this->getDoctrine()->getManager();
            $userRepo=$em->getRepository('AppBundle:User');

            $startStr = strtotime($data['startDate'].' 00:00:00');
            $startDate = date('Y-m-d H:i:s',$startStr);

            $endStr = strtotime($data['endDate'].'  23:59:59');
            $endDate = date('Y-m-d H:i:s',$endStr);

            $totalActiveUsers = $userRepo->getTotalActiveUsers();
            $totalActiveSocialUsers = $userRepo->getTotalSocialActiveUsers();

            $activeGoogleUsers = $userRepo->getActiveGoogleUsers($startDate,$endDate);
            $activeFacebookUsers = $userRepo->getActiveFacebookUsers($startDate,$endDate);
            $activeNormalUsers = $userRepo->getActiveNormalUsers($startDate,$endDate);


            $data = array(
                'totalActiveUsers' => $totalActiveUsers ,
                'totalActiveSocialUsers' => $totalActiveSocialUsers,
                'activeGoogleUsers' => $activeGoogleUsers,
                'activeFacebookUsers' => $activeFacebookUsers,
                'activeNormalUsers' => $activeNormalUsers
            );

            return $this->_createJsonResponse('success', array('successData'=>array('userData'=>$data)), 200);
        }else{
            return $this->_createJsonResponse('error', array('errorTitle'=>"You are not authorized to see this page."), 400);
        }

    }

    /**
     * Get Login and Registration User data
     *
     */
    public function getLoginAndRegistrationUserDataAction(Request $request){
        $user = $this->container->get('security.token_storage')->getToken()->getUser();

        if(in_array('ROLE_ADMIN_USER',$user->getRoles(),true)){

            $content = $request->getContent();
            $data = json_decode($content, true);
            $em = $this->getDoctrine()->getManager();
            $logRepo=$em->getRepository('AppBundle:Log');

            $startStr = strtotime($data['startDate'].' 00:00:00');
            $startDate = date('Y-m-d H:i:s',$startStr);

            $endStr = strtotime($data['endDate'].'  23:59:59');
            $endDate = date('Y-m-d H:i:s',$endStr);


            $daysArray =array();
            $loginArray =array();
            $registrationArray=array();


            $loggedInUserLog = $logRepo->getUsersLog($startDate,$endDate,"Login");
            $registrationUserLog = $logRepo->getUsersLog($startDate,$endDate,"Registration");


            $begin=date_create($startDate);
            $end=date_create($endDate);
            $interval = new \DateInterval('P1D');
            $period=new \DatePeriod($begin,$interval,$end);

            foreach ($period as $d){
                $daysArray[]=$d->format('d-M-Y');
                $loginCount=0;
                $registrationCount=0;
                foreach($loggedInUserLog as $row){
                    if(!strcmp($d->format('d-M-Y'),$row['logDate'])){
                        $loginCount=(int)$row['count'];
                    }
                }
                foreach($registrationUserLog as $row){
                    if(!strcmp($d->format('d-M-Y'),$row['logDate'])){
                        $registrationCount=(int)$row['count'];
                    }
                }
                $loginArray[]=$loginCount;
                $registrationArray[]=$registrationCount;
            }


            $data = array(
                'dateData' => $daysArray ,
                'loginData' => $loginArray,
                'registrationData' => $registrationArray,
            );

            return $this->_createJsonResponse('success', array('successData'=>array('userData'=>$data)), 200);
        }else{
            return $this->_createJsonResponse('error', array('errorTitle'=>"You are not authorized to see this page."), 400);
        }
    }


    /**
     * Get Book Deal Data
     *
     */
    public function getBookDealAnDContactDataAction(Request $request){
        $user = $this->container->get('security.token_storage')->getToken()->getUser();

        if(in_array('ROLE_ADMIN_USER',$user->getRoles(),true)){

            $content = $request->getContent();
            $data = json_decode($content, true);
            $em = $this->getDoctrine()->getManager();
            $logRepo=$em->getRepository('AppBundle:Log');

            $startStr = strtotime($data['startDate'].' 00:00:00');
            $startDate = date('Y-m-d H:i:s',$startStr);

            $endStr = strtotime($data['endDate'].'  23:59:59');
            $endDate = date('Y-m-d H:i:s',$endStr);


            $daysArray =array();
            $bookDealArray =array();
            $contactArray = array();



            $bookDealLog = $logRepo->getUsersLog($startDate,$endDate,"Add Book Deal");
            $contactLog = $logRepo->getUsersLog($startDate,$endDate,"Buyer Contacted");


            $begin=date_create($startDate);
            $end=date_create($endDate);
            $interval = new \DateInterval('P1D');
            $period=new \DatePeriod($begin,$interval,$end);

            foreach ($period as $d){
                $daysArray[]=$d->format('d-M-Y');
                $bookDealCount=0;
                $contactCount=0;
                foreach($bookDealLog as $row){
                    if(!strcmp($d->format('d-M-Y'),$row['logDate'])){
                        $bookDealCount=(int)$row['count'];
                    }
                }
                foreach($contactLog as $row){
                    if(!strcmp($d->format('d-M-Y'),$row['logDate'])){
                        $contactCount=(int)$row['count'];
                    }
                }
                $bookDealArray[]=$bookDealCount;
                $contactArray[]=$contactCount;
            }


            $data = array(
                'dateData' => $daysArray ,
                'bookDealData' => $bookDealArray,
                'contactData' => $contactArray
            );

            return $this->_createJsonResponse('success', array('successData'=>array('userData'=>$data)), 200);
        }else{
            return $this->_createJsonResponse('error', array('errorTitle'=>"You are not authorized to see this page."), 400);
        }
    }

    /**
     * Get Book  Deal Method Data
     *
     */
    public function getBookDealMethodDataAction(Request $request){
        $user = $this->container->get('security.token_storage')->getToken()->getUser();

        if(in_array('ROLE_ADMIN_USER',$user->getRoles(),true)){

            $content = $request->getContent();
            $data = json_decode($content, true);
            $em = $this->getDoctrine()->getManager();
            $bookDealRepo=$em->getRepository('AppBundle:BookDeal');

            $startStr = strtotime($data['startDate'].' 00:00:00');
            $startDate = date('Y-m-d H:i:s',$startStr);

            $endStr = strtotime($data['endDate'].'  23:59:59');
            $endDate = date('Y-m-d H:i:s',$endStr);


            $bookDealData = $bookDealRepo->getBookDealMethodData($startDate,$endDate);
            $bookDealArray=array(
                'buyerToSellerDeals'=>0,
                'sellerToBuyerDeals'=>0,
                'messageBoardDeals'=>0,
            );

            foreach ($bookDealData as $method){
                if(!strcmp($method['book_contact_method'],"buyerToSeller")){
                    $bookDealArray['buyerToSellerDeals'] = (int)$method['count'];
                }elseif(!strcmp($method['book_contact_method'],"sellerToBuyer")){
                    $bookDealArray['sellerToBuyerDeals'] = (int)$method['count'];
                }elseif(!strcmp($method['book_contact_method'],"student2studentBoard")){
                    $bookDealArray['messageBoardDeals'] = (int)$method['count'];
                }

            }

            return $this->_createJsonResponse('success', array('successData'=>array('bookDealData'=>$bookDealArray)), 200);
        }else{
            return $this->_createJsonResponse('error', array('errorTitle'=>"You are not authorized to see this page."), 400);
        }
    }

    public function _createJsonResponse($key, $data,$code)
    {
        $serializer = $this->container->get('jms_serializer');
        $json = $serializer->serialize([$key => $data], 'json');
        $response = new Response($json, $code);
        return $response;
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
            $searchQuery = filter_var($data["searchQuery"], FILTER_SANITIZE_STRING);
            $emailQuery = filter_var($data["emailQuery"], FILTER_SANITIZE_STRING);
            $fullNameQuery = filter_var($data["fullNameQuery"], FILTER_SANITIZE_STRING);
            $universityNameQuery = filter_var($data["universityNameQuery"], FILTER_SANITIZE_STRING);
            $campusNameQuery = filter_var($data["campusNameQuery"], FILTER_SANITIZE_STRING);
            $enabledQuery = $data["enabledQuery"];
            $pageNumber = $data["pageNumber"];
            $sort = $data["sort"];



            $totalNumber = $userRepo->getApprovedUserSearchNumber($searchQuery,$emailQuery,$fullNameQuery,$universityNameQuery,$campusNameQuery,$enabledQuery);
            $users = $userRepo->getApprovedUserSearchResult($searchQuery,$emailQuery,$fullNameQuery,$universityNameQuery,$campusNameQuery,$enabledQuery, $pageNumber, $pageSize,$sort);

            for($i=0;$i<count($users);$i++){
                $users[$i]['registrationDateTime'] =$users[$i]['registrationDateTime']->format('g:i A, d M Y');
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
            $searchQuery = filter_var($data["searchQuery"], FILTER_SANITIZE_STRING);
            $emailQuery = filter_var($data["emailQuery"], FILTER_SANITIZE_STRING);
            $pageNumber = $data["pageNumber"];
            $sort = $data["sort"];


            $totalNumber = $userRepo->getAdminUserSearchNumber($searchQuery,$emailQuery);
            $users = $userRepo->getAdminUserSearchResult($searchQuery,$emailQuery, $pageNumber, $pageSize,$sort);

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

                    $editedUser->setUserName($request_data['username']);
                    $editedUser->setEnabled($request_data['enabled']);
                    $editedUser->setAdminApproved('Yes');
                    $em->persist($editedUser);
                    $em->flush();

                    $logData = array(
                        'user'=>$user->getId(),
                        'logType'=>"Update User",
                        'logDateTime'=>gmdate('Y-m-d H:i:s'),
                        'logDescription'=> $editedUser->isEnabled()?$user->getUsername()." has updated & activated user named ".$editedUser->getUsername():$user->getUsername()." has updated & deactivated user named ".$editedUser->getUsername(),
                        'userIpAddress'=>$this->container->get('request')->getClientIp(),
                        'logUserType'=> in_array("ROLE_ADMIN_USER",$user->getRoles())?"Admin User":"Normal User"
                    );
                    $this->_saveLog($logData);

                    return $this->_createJsonResponse('success',array(
                        'successTitle'=>"User Approved",
                    ),200);

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


}
