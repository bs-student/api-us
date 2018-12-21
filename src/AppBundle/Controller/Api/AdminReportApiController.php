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


class AdminReportApiController extends Controller
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
                $daysArray[]=$d->format('l,d-M-Y');
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
            $bookDealRepo=$em->getRepository('AppBundle:BookDeal');

            $startStr = strtotime($data['startDate'].' 00:00:00');
            $startDate = date('Y-m-d H:i:s',$startStr);

            $endStr = strtotime($data['endDate'].'  23:59:59');
            $endDate = date('Y-m-d H:i:s',$endStr);


            $daysArray =array();
            $bookDealArray =array();
            $contactArray = array();
            $soldArray = array();

            $totalActiveBookDeals = $bookDealRepo->getTotalActiveBookDeals();
            $totalSoldBookDeals = $bookDealRepo->getTotalSoldBookDeals();

            $bookDealLog = $logRepo->getUsersLog($startDate,$endDate,"Add Book Deal");
            $contactLog = $logRepo->getUsersLog($startDate,$endDate,"Buyer Contacted");
            $soldLog = $logRepo->getUsersLog($startDate,$endDate,"Sold Book");


            $begin=date_create($startDate);
            $end=date_create($endDate);
            $interval = new \DateInterval('P1D');
            $period=new \DatePeriod($begin,$interval,$end);

            foreach ($period as $d){
                $daysArray[]=$d->format('l,d-M-Y');
                $bookDealCount=0;
                $contactCount=0;
                $soldCount=0;
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
                foreach($soldLog as $row){
                    if(!strcmp($d->format('d-M-Y'),$row['logDate'])){
                        $soldCount=(int)$row['count'];
                    }
                }
                $bookDealArray[]=$bookDealCount;
                $contactArray[]=$contactCount;
                $soldArray[]=$soldCount;
            }


            $data = array(
                'dateData' => $daysArray ,
                'bookDealData' => $bookDealArray,
                'soldData'=>$soldArray,
                'contactData' => $contactArray,
                'createdBookDeals'=>(int)$totalActiveBookDeals,
                'soldBookDeals'=>(int)$totalSoldBookDeals
            );

            return $this->_createJsonResponse('success', array('successData'=>array('bookDealData'=>$data)), 200);
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

    /**
     * Get Universities User Data
     *
     */
    public function getUniversitiesUserDataAction(Request $request){
        $user = $this->container->get('security.token_storage')->getToken()->getUser();

        if(in_array('ROLE_ADMIN_USER',$user->getRoles(),true)){

            $content = $request->getContent();
            $data = json_decode($content, true);
            $em = $this->getDoctrine()->getManager();
            $universityRepo=$em->getRepository('AppBundle:University');


            $pageSize = $data["pageSize"];
            $searchQuery = filter_var($data["searchQuery"], FILTER_SANITIZE_STRING);
            $pageNumber = $data["pageNumber"];


            $totalActiveUniversityCountData = $universityRepo->getTotalActiveUniversityCountData();
            $totalActiveUniversityUserNumberData = $universityRepo->getTotalActiveUniversityUserNumberData($searchQuery);
            $totalActiveUniversityUserData = $universityRepo->getTotalActiveUniversityUserData($pageSize,$pageNumber,$searchQuery);

            $data = array(
                'universitySearchData' => $totalActiveUniversityUserData ,
                'universityNumberData' => $totalActiveUniversityUserNumberData,
                'totalUniversityCountData'=>$totalActiveUniversityCountData
            );

            return $this->_createJsonResponse('success', array('successData'=>array('universityData'=>$data)), 200);
        }else{
            return $this->_createJsonResponse('error', array('errorTitle'=>"You are not authorized to see this page."), 400);
        }
    }

    /**
     * Get Access Token From Google for email st2sttextbook@gmail.com
     *
     */
    public function getGoogleAccessTokenAction()
    {
        $user = $this->container->get('security.token_storage')->getToken()->getUser();

        if(in_array('ROLE_ADMIN_USER',$user->getRoles(),true)){
            require_once $this->get('kernel')->getRootDir().'/../vendor/google/apiclient/autoload.php';

            $serviceAccountName = $this->container->getParameter('google_service_account_credential')['service_account_name'];
            $keyFile = $this->get('kernel')->getRootDir().$this->container->getParameter('google_service_account_credential')['p12_file_path'];
            $scope = $this->container->getParameter('google_service_account_credential')['scope'];

            $key = file_get_contents($keyFile);
            $auth = new \Google_Auth_AssertionCredentials(
                $serviceAccountName,
                array($scope),
                $key
            );

            $client = new \Google_Client();
            $client->setScopes(array($scope));
            $client->setAssertionCredentials($auth);
            $client->getAuth()->refreshTokenWithAssertion();
            $accessToken = $client->getAccessToken();
            return $this->_createJsonResponse('success', array('successData'=>array('accessToken'=>json_decode($accessToken,true))), 200);

        }else{
            return $this->_createJsonResponse('error', array('errorTitle'=>"You are not authorized to see this page."), 400);
        }
    }



    function myComparison($a, $b) {

        $a = $a['count'];
        $b = $b['count'];

        if ($a == $b)
        {
            return 0;
        }

        return ($a < $b) ? 1 : -1;
    }

    public function _createJsonResponse($key, $data,$code)
    {
        $serializer = $this->container->get('jms_serializer');
        $json = $serializer->serialize([$key => $data], 'json');
        $response = new Response($json, $code);
        return $response;

    }
}
