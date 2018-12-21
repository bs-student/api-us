<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\Campus;
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

class CampusManagementApiController extends Controller
{


    /**
     * Campus List By university
     */
    public function campusListByUniversityAction(Request $request)
    {

        $content = $request->getContent();
        $data = json_decode($content, true);
        $universityId = $data["universityId"];

        $em = $this->getDoctrine()->getManager();
        $campusRepo = $em->getRepository('AppBundle:Campus');

        $campuses = $campusRepo->getCampusesByUniversityId($universityId);

        return $this->_createJsonResponse('success',array('successData'=>$campuses),200 );


    }

    /**
     * Update Campus
     */
    public function updateCampusAction(Request $request)
    {
        //Initialize Repositories
        $em = $this->getDoctrine()->getManager();
        $campusRepo = $em->getRepository('AppBundle:Campus');
        $serializer = $this->container->get('jms_serializer');


        //Getting Request Data
        $data = null;
        $content = $request->getContent();
        if (!empty($content)) {
            $data = json_decode($content, true);
        }
        if (count($data) > 0) {
            if (array_key_exists("id", $data)) {


                $campus = $campusRepo->findOneBy(array(
                    'id' => $data['id']
                ));

                $oldCampusName = $campus->getCampusName();
                $oldCampusStatus = $campus->getCampusStatus();
                $oldCampusId = $campus->getId();


                $campus_update_form = $this->createForm(new CampusType(), $campus);
                $campus_update_form->remove('state');

                $campus_update_form->submit($data);


                if ($campus_update_form->isValid()) {

                    $em->persist($campus);
                    $em->flush();

                    $array = array(
                        'successTitle' => "Campus Updated Successfully",
                        'successDescription' => "Campus has been updated. please check the list for update result."
                    );
                    return $this->_createJsonResponse('success', $array,200);

                } else {

                    $array = array(
                        'errorTitle' => "Campus Could not be Updated",
                        'errorDescription' => "Sorry there is a problem with the form data. Please check and submit again.",
                        'errorData'=>array(
                            'campusStatus' => $oldCampusStatus,
                            'campusName' => $oldCampusName,
                            'campusId' => $oldCampusId
                        )

                    );
                    return $this->_createJsonResponse('error', $array,400);

                }


            } else {
                return $this->_createJsonResponse('error', array('errorTitle' => "Error on submitting Data", 'errorDescription' => "Please check the fields and try again."),400);
            }

        } else {
            return $this->_createJsonResponse('error', array('errorTitle' => "Error on submitting Data", 'errorDescription' => "Please check the fields and try again."),400);
        }


    }

    /**
     * Save new Campus
     */
    public function saveNewCampusAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $serializer = $this->container->get('jms_serializer');
        $universityRepo = $em->getRepository("AppBundle:University");
        $stateRepo = $em->getRepository("AppBundle:State");
        $request_data = json_decode($request->getContent(), true);

        $error = false;
        if (array_key_exists('universityId', $request_data)) {
            $universityEntity = $universityRepo->findOneById($request_data['universityId']);

            $campus = null;
            $state = null;
            $campusEntity = new Campus();
            if (array_key_exists('campusName', $request_data)){
                $campusEntity->setCampusName($request_data['campusName']);
                $campusEntity->setCampusStatus("Activated");
            } else{
                $error = true;
            }
            if (array_key_exists('state', $request_data)) {
                $campusEntity->setState($stateRepo->findOneById($request_data['state']));
            }else{
                $error =true;
            }
            $universityEntity->addCampus($campusEntity);
            if(!$error){
                $em->persist($universityEntity);
                $em->flush();
                return $this->_createJsonResponse('success',array('successTitle'=>"Campus Created Successfully",'successDescription'=>"Campus has been added to the University"),200);
            }else{
                return $this->_createJsonResponse('error',array('errorTitle'=>"Campus was not created",'errorDescription'=>"Sorry, please check the form and submit again"),400);
            }
        }

    }


    /**
     * Get Campus Details With University And State
     */
    public function campusDetailsWithUniversityAndStateAction(Request $request)
    {
        $requestData = json_decode($request->getContent(), true);
        $em = $this->getDoctrine()->getManager();

        $campusRepo = $em->getRepository("AppBundle:Campus");
        $campusData = $campusRepo->getCampusDetailsWithUniversityAndState($requestData['campusId']);

        return $this->_createJsonResponse('success',array('successData'=>$campusData),200);

    }

    /**
     * Delete University
     */
    //TODO SOMETHING WRONG WITH FOLLOWING FUNCTION
    public function deleteUniversityAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $serializer = $this->container->get('jms_serializer');
        $universityRepo = $em->getRepository("AppBundle:University");
        $request_data = json_decode($request->getContent(), true);

        $university = $universityRepo->findOneById($request_data['deleteId']);

        $message_array = null;
        if (!$university) {
            $message_array = array(
                'errorTitle' => "University Could not be Deleted",
                'errorDescription'=>'No University was found.'
            );
            return $this->_createJsonResponse('error',$message_array,400);

        } else {
            $em->remove($university);
            $em->flush();
            $message_array = array(
                'successTitle' => "University has been removed Removed",

            );
            return $this->_createJsonResponse('success',$message_array,200);
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
