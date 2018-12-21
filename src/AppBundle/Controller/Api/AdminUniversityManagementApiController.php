<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\Campus;
use AppBundle\Entity\Log;
use AppBundle\Form\Type\LogType;
use AppBundle\Form\Type\UniversityType;
use Doctrine\Common\Collections\ArrayCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Form\Type\CampusType;
use AppBundle\Entity\University;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Response;

class AdminUniversityManagementApiController extends Controller
{
    /**
     * get All NonApproved Universities Admin api
     */
    public function getAllNonApprovedUniversitiesAction(Request $request){
        $user = $this->container->get('security.token_storage')->getToken()->getUser();

        if(in_array('ROLE_ADMIN_USER',$user->getRoles(),true)){

            $content = $request->getContent();
            $data = json_decode($content, true);
            $em = $this->getDoctrine()->getManager();
            $universityRepo=$em->getRepository('AppBundle:University');

            $pageSize = $data["pageSize"];
            $searchQuery = filter_var($data["searchQuery"], FILTER_SANITIZE_STRING);
            $pageNumber = $data["pageNumber"];
            $sort = $data["sort"];

            $totalNumber = $universityRepo->getAllNonApprovedUniversitiesSearchNumber($searchQuery);
            $searchResults= $universityRepo->getAllNonApprovedUniversitiesSearchResult($searchQuery, $pageNumber, $pageSize,$sort);

            for($i=0;$i<count($searchResults);$i++){
                $searchResults[$i]['campuses']=array();
                $campuses = $universityRepo->findOneById($searchResults[$i]['universityId'])->getCampuses();
                $searchResults[$i]['creationDateTime']=$searchResults[$i]['creationDateTime']->format('h:i A, d-M-Y');
                foreach($campuses as $campus){
                    array_push($searchResults[$i]['campuses'],array(
                        'campusId'=>$campus->getId(),
                        'campusName'=>$campus->getCampusName(),
                        'stateShortName'=>$campus->getState()->getStateShortName(),
                        'stateName'=>$campus->getState()->getStateName(),
                        'countryName'=>$campus->getState()->getCountry()->getCountryName(),
                        'campusStatus'=>$campus->getCampusStatus()
                    ));
                }

            }

            $data = array(
                'totalUniversities' => $searchResults ,
                'totalNumber' => $totalNumber
            );

            return $this->_createJsonResponse('success', array('successData'=>array('universities'=>$data)), 200);
        }else{
            return $this->_createJsonResponse('error', array('errorTitle'=>"You are not authorized to see this page."), 400);
        }
    }

    /**
     * get All Activated Universities Admin api
     */
    public function getAllActivatedUniversitiesAction(Request $request){
        $user = $this->container->get('security.token_storage')->getToken()->getUser();

        if(in_array('ROLE_ADMIN_USER',$user->getRoles(),true)){

            $content = $request->getContent();
            $data = json_decode($content, true);
            $em = $this->getDoctrine()->getManager();
            $universityRepo=$em->getRepository('AppBundle:University');

            $pageSize = $data["pageSize"];
            $searchQuery = filter_var($data["searchQuery"], FILTER_SANITIZE_STRING);
            $pageNumber = $data["pageNumber"];
            $sort = $data["sort"];

            $totalNumber = $universityRepo->getAllActivatedUniversitiesSearchNumber($searchQuery);
            $searchResults= $universityRepo->getAllActivatedUniversitiesSearchResult($searchQuery, $pageNumber, $pageSize,$sort);

            for($i=0;$i<count($searchResults);$i++){
                $searchResults[$i]['campuses']=array();
                $campuses = $universityRepo->findOneById($searchResults[$i]['universityId'])->getCampuses();
                $searchResults[$i]['creationDateTime']=$searchResults[$i]['creationDateTime']->format('h:i A, d-M-Y');
                foreach($campuses as $campus){
                    array_push($searchResults[$i]['campuses'],array(
                        'campusId'=>$campus->getId(),
                        'campusName'=>$campus->getCampusName(),
                        'stateShortName'=>$campus->getState()->getStateShortName(),
                        'stateName'=>$campus->getState()->getStateName(),
                        'countryName'=>$campus->getState()->getCountry()->getCountryName(),
                        'campusStatus'=>$campus->getCampusStatus()
                    ));
                }

            }

            $data = array(
                'totalUniversities' => $searchResults ,
                'totalNumber' => $totalNumber
            );

            return $this->_createJsonResponse('success', array('successData'=>array('universities'=>$data)), 200);
        }else{
            return $this->_createJsonResponse('error', array('errorTitle'=>"You are not authorized to see this page."), 400);
        }
    }

    /**
     * get All Deactivated Universities Admin api
     */
    public function getAllDeactivatedUniversitiesAction(Request $request){
        $user = $this->container->get('security.token_storage')->getToken()->getUser();

        if(in_array('ROLE_ADMIN_USER',$user->getRoles(),true)){

            $content = $request->getContent();
            $data = json_decode($content, true);
            $em = $this->getDoctrine()->getManager();
            $universityRepo=$em->getRepository('AppBundle:University');

            $pageSize = $data["pageSize"];
            $searchQuery = filter_var($data["searchQuery"], FILTER_SANITIZE_STRING);
            $pageNumber = $data["pageNumber"];
            $sort = $data["sort"];

            $totalNumber = $universityRepo->getAllDeactivatedUniversitiesSearchNumber($searchQuery);
            $searchResults= $universityRepo->getAllDeactivatedUniversitiesSearchResult($searchQuery, $pageNumber, $pageSize,$sort);

            for($i=0;$i<count($searchResults);$i++){
                $searchResults[$i]['campuses']=array();
                $campuses = $universityRepo->findOneById($searchResults[$i]['universityId'])->getCampuses();
                $searchResults[$i]['creationDateTime']=$searchResults[$i]['creationDateTime']->format('h:i A, d-M-Y');
                foreach($campuses as $campus){
                    array_push($searchResults[$i]['campuses'],array(
                        'campusId'=>$campus->getId(),
                        'campusName'=>$campus->getCampusName(),
                        'stateShortName'=>$campus->getState()->getStateShortName(),
                        'stateName'=>$campus->getState()->getStateName(),
                        'countryName'=>$campus->getState()->getCountry()->getCountryName(),
                        'campusStatus'=>$campus->getCampusStatus()
                    ));
                }

            }

            $data = array(
                'totalUniversities' => $searchResults ,
                'totalNumber' => $totalNumber
            );

            return $this->_createJsonResponse('success', array('successData'=>array('universities'=>$data)), 200);
        }else{
            return $this->_createJsonResponse('error', array('errorTitle'=>"You are not authorized to see this page."), 400);
        }
    }

    /**
     * Save Edited University Data Only Admin api
     */
    public function saveEditedUniversityDataOnlyAction(Request $request){
        $user = $this->container->get('security.token_storage')->getToken()->getUser();

        if(in_array('ROLE_ADMIN_USER',$user->getRoles(),true)){

            $content = $request->getContent();
            $data = json_decode($content, true);
            $em = $this->getDoctrine()->getManager();
            $universityRepo=$em->getRepository('AppBundle:University');

            if(array_key_exists('universityId',$data)){
                $university = $universityRepo->findOneById($data['universityId']);
                $university->setUniversityName($data['universityName']);
                $university->setUniversityUrl(array_key_exists('universityUrl',$data)?$data['universityUrl']:'');
                if($data["adminApproved"]=="Yes"){
                    $university->setAdminApproved("Yes");
                }else{
                    $university->setAdminApproved("No");
                }
                $university->setUniversityStatus($data['universityStatus']);
                $em->persist($university);
                $em->flush();

                $logData = array(
                    'user'=>$user->getId(),
                    'logType'=>"Update University",
                    'logDateTime'=>gmdate('Y-m-d H:i:s'),
                    'logDescription'=> $university->getUniversityStatus()=="Activated"?$user->getUsername()." has updated & activated university named '".$university->getUniversityname():$user->getUsername()." has updated & deactivated university named '".$university->getUniversityname()."'",
                    'userIpAddress'=>$this->container->get('request')->getClientIp(),
                    'logUserType'=> in_array("ROLE_ADMIN_USER",$user->getRoles())?"Admin User":"Normal User"
                );
                $this->_saveLog($logData);

                return $this->_createJsonResponse('success', array(
                        'successTitle'=>"University has been updated successfully",
                        'successData'=>array(
                            'universityName'=>$university->getUniversityName(),
                            'universityUrl'=>$university->getUniversityUrl(),
                            'universityStatus'=>$university->getUniversityStatus()
                        )
                    ), 200
                );

            }else{
                return $this->_createJsonResponse('error', array('errorTitle'=>"Invalid Data Provided."), 400);
            }

        }else{
            return $this->_createJsonResponse('error', array('errorTitle'=>"You are not authorized to see this page."), 400);
        }

    }

    /**
     * Approve Multiple Universities Admin api
     */
    public function approveMultipleUniversitiesAction(Request $request){
        $user = $this->container->get('security.token_storage')->getToken()->getUser();

        if(in_array('ROLE_ADMIN_USER',$user->getRoles(),true)){

            $content = $request->getContent();
            $data = json_decode($content, true);
            $em = $this->getDoctrine()->getManager();
            $universityRepo=$em->getRepository('AppBundle:University');

            if(count($data)>0){
                $updated = $universityRepo->approveUniversities($data);

                $universities = "";
                foreach($data as $universityRow){
                    $universities.="'".$universityRow['universityName']."', ";
                }

                $logData = array(
                    'user'=>$user->getId(),
                    'logType'=>"Approve University",
                    'logDateTime'=>gmdate('Y-m-d H:i:s'),
                    'logDescription'=> $user->getUsername()." has approved universities named '".$universities,
                    'userIpAddress'=>$this->container->get('request')->getClientIp(),
                    'logUserType'=> in_array("ROLE_ADMIN_USER",$user->getRoles())?"Admin User":"Normal User"
                );
                $this->_saveLog($logData);

                if($updated){
                    return $this->_createJsonResponse('success', array(
                            'successTitle'=>"Universities has been approved successfully"
                        ), 200
                    );
                }else{
                    return $this->_createJsonResponse('error', array('errorTitle'=>"Sorry, Universities are not updated"), 400);
                }
            }

        }else{
            return $this->_createJsonResponse('error', array('errorTitle'=>"You are not authorized to see this page."), 400);
        }
    }

    /**
     * update university details
     */
    public function updateUniversityDetailsAction(Request $request){
        $user = $this->container->get('security.token_storage')->getToken()->getUser();

        if(in_array('ROLE_ADMIN_USER',$user->getRoles(),true)){

            $content = $request->getContent();
            $data = json_decode($content, true);

            $em = $this->getDoctrine()->getManager();
            $universityRepo = $em->getRepository('AppBundle:University');

            $university = $universityRepo->findOneById($data['universityId']);

            for($i=0; $i<count($data['campuses']); $i++){
                $data['campuses'][$i]['id'] = $data['campuses'][$i]['campusId'];
                unset($data['campuses'][$i]['campusId']);

            }

            for($i=0; $i<count($data['newCampuses']); $i++){
                unset($data['newCampuses'][$i]['campusId']);
                $data['newCampuses'][$i]['campusStatus']="Activated";
                array_push($data['campuses'],$data['newCampuses'][$i]);
            }


            $universityForm = $this->createForm(new UniversityType(), $university);
            $universityForm->remove('creationDateTime');
            $universityForm->remove('referral');

            $universityForm->submit($data);

            if ($universityForm->isValid()) {
                $em->persist($university);
                $em->flush();

                $logData = array(
                    'user'=>$user->getId(),
                    'logType'=>"Update University",
                    'logDateTime'=>gmdate('Y-m-d H:i:s'),
                    'logDescription'=> $university->getUniversityStatus()=="Activated"?$user->getUsername()." has updated in details & activated university named '".$university->getUniversityname():$user->getUsername()." has updated & deactivated university named '".$university->getUniversityname()."'",
                    'userIpAddress'=>$this->container->get('request')->getClientIp(),
                    'logUserType'=> in_array("ROLE_ADMIN_USER",$user->getRoles())?"Admin User":"Normal User"
                );
                $this->_saveLog($logData);

                return $this->_createJsonResponse('success', array(
                        'successTitle'=>"University has been updated & approved successfully"
                    ), 200
                );


            } else {
                return $this->_createJsonResponse('error', array("errorTitle"=>"Sorry, Could not update university",
                    "errorDescription" => "Check the form and submit again",
                    "errorData" => $universityForm),
                    400);
            }

        }else{
            return $this->_createJsonResponse('error', array('errorTitle'=>"You are not authorized to see this page."), 400);
        }
    }

    /**
     * Get all similar universities
     */
    public function getAllSimilarUniversitiesAction(Request $request){
        $user = $this->container->get('security.token_storage')->getToken()->getUser();

        if(in_array('ROLE_ADMIN_USER',$user->getRoles(),true)){

            $content = $request->getContent();
            $data = json_decode($content, true);
            $em = $this->getDoctrine()->getManager();
            $universityRepo=$em->getRepository('AppBundle:University');

            $pageSize = $data["pageSize"];
            $searchQuery = filter_var($data["searchQuery"], FILTER_SANITIZE_STRING);
            $pageNumber = $data["pageNumber"];
            $sort = $data["sort"];


            $searchResults= $universityRepo->getSimilarUniversitiesSearchResult($searchQuery, $pageNumber, $pageSize,$sort);
            $totalNumber = $universityRepo->getSimilarUniversitiesSearchNumber($searchQuery);

            for($i=0;$i<count($searchResults);$i++){
                if($searchResults[$i]['universityId']!=$data['universityId']){
                    $searchResults[$i]['campuses']=array();
                    $campuses = $universityRepo->findOneById($searchResults[$i]['universityId'])->getCampuses();
                    $searchResults[$i]['creationDateTime']=$searchResults[$i]['creationDateTime']->format('h:i A, d-M-Y');
                    foreach($campuses as $campus){
                        array_push($searchResults[$i]['campuses'],array(
                            'campusId'=>$campus->getId(),
                            'campusName'=>$campus->getCampusName(),
                            'stateShortName'=>$campus->getState()->getStateShortName(),
                            'stateName'=>$campus->getState()->getStateName(),
                            'countryName'=>$campus->getState()->getCountry()->getCountryName(),
                            'campusStatus'=>$campus->getCampusStatus()
                        ));
                    }
                }else{
                    unset($searchResults[$i]);
                }


            }

            $data = array(
                'totalUniversities' => $searchResults ,
                'totalNumber' => $totalNumber
            );

            return $this->_createJsonResponse('success', array('successData'=>array('universities'=>$data)), 200);
        }else{
            return $this->_createJsonResponse('error', array('errorTitle'=>"You are not authorized to see this page."), 400);
        }
    }

    /**
     * merge universities
     */
    public function mergeUniversitiesAction(Request $request){
        $user = $this->container->get('security.token_storage')->getToken()->getUser();

        if(in_array('ROLE_ADMIN_USER',$user->getRoles(),true)){

            $content = $request->getContent();
            $data = json_decode($content, true);
            $em = $this->getDoctrine()->getManager();
            $universityRepo=$em->getRepository('AppBundle:University');

            $mergeFromUniversity = $universityRepo->findOneById($data['mergeFromUniversityId']);
            $mergeToUniversity = $universityRepo->findOneById($data['mergeToUniversityId']);


            foreach($mergeFromUniversity->getCampuses() as $campus){
                $campus->setUniversity($mergeToUniversity);
                $em->persist($campus);
                $em->flush();
            }
            $em->clear();

            //Reinitializing after merging campus
            $mergeFromUniversity = $universityRepo->findOneById($data['mergeFromUniversityId']);
            $em->remove($mergeFromUniversity);
            $em->flush();

            $logData = array(
                'user'=>$user->getId(),
                'logType'=>"Merge University",
                'logDateTime'=>gmdate('Y-m-d H:i:s'),
                'logDescription'=> $user->getUsername()." has merged university named '".$mergeFromUniversity->getUniversityName()."' to university named '".$mergeToUniversity->getUniversityName()."'",
                'userIpAddress'=>$this->container->get('request')->getClientIp(),
                'logUserType'=> in_array("ROLE_ADMIN_USER",$user->getRoles())?"Admin User":"Normal User"
            );
            $this->_saveLog($logData);

            return $this->_createJsonResponse('success', array(
                    'successTitle'=>"University has been merged successfully"
                ), 200
            );

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
