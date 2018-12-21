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


class AdminLogApiController extends Controller
{


    /**
     * Get Logs
     *
     */
    public function getLogAction(Request $request){
        $user = $this->container->get('security.token_storage')->getToken()->getUser();

        if(in_array('ROLE_ADMIN_USER',$user->getRoles(),true)){

            $content = $request->getContent();
            $data = json_decode($content, true);
            $em = $this->getDoctrine()->getManager();
            $logRepo=$em->getRepository('AppBundle:Log');


            $pageSize = $data["pageSize"];
            $usernameQuery = filter_var($data["usernameQuery"], FILTER_SANITIZE_STRING);
            $logTypeQuery = filter_var($data["logTypeQuery"], FILTER_SANITIZE_STRING);
            $logUserTypeQuery = filter_var($data["logUserTypeQuery"], FILTER_SANITIZE_STRING);

            $pageNumber = $data["pageNumber"];
            $sort = $data["sort"];

            $totalNumber = $logRepo->getLogSearchNumber($usernameQuery,$logTypeQuery,$logUserTypeQuery);
            $logs = $logRepo->getLogSearchResult($usernameQuery,$logTypeQuery,$logUserTypeQuery, $pageNumber, $pageSize,$sort);


            for($i=0;$i<count($logs);$i++){
                $logs[$i]['logDateTime'] =$logs[$i]['logDateTime']->format('g:i A, d M Y');
            }


            $data = array(
                'totalLogs' => $logs,
                'totalNumber' => $totalNumber
            );

            return $this->_createJsonResponse('success', array('successData'=>array('logs'=>$data)), 200);
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
}
