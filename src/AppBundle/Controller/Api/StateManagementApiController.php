<?php

namespace AppBundle\Controller\Api;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\State;
use AppBundle\Form\StateType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
/**
 * State controller.
 *
 */
class StateManagementApiController extends Controller
{

    /**
     * Get all the states under a country
     */
    public function statesByCountryAction(Request $request)
    {

        $request_data = json_decode($request->getContent());

        $em = $this->getDoctrine()->getManager();


        $states = $em->getRepository('AppBundle:State')->findByCountryId($request_data->countryId);

        return $this->_createJsonResponse('success',array('successData'=>$states),200);

    }


    /**
     * Get all the states under a country Admin api
     */
    public function statesByCountryAdminAction(Request $request)
    {

        $request_data = json_decode($request->getContent());

        $em = $this->getDoctrine()->getManager();


        $states = $em->getRepository('AppBundle:State')->findByCountryId($request_data->countryId);

        return $this->_createJsonResponse('success',array('successData'=>$states),200);

    }

    public function _createJsonResponse($key, $data,$code)
    {
        $serializer = $this->container->get('jms_serializer');
        $json = $serializer->serialize([$key => $data], 'json');
        $response = new Response($json, $code);
        return $response;
    }


}
