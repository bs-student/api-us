<?php

namespace AppBundle\Controller\Api;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\Country;
use AppBundle\Form\CountryType;

/**
 * Country controller.
 *
 */
class CountryManagementApiController extends Controller
{

    /**
     * Get List of all Countries
     */
    public function countryListAction()
    {
        $em = $this->getDoctrine()->getManager();

        $countries = $em->getRepository('AppBundle:Country')->findAllCountry();

        return $this->_createJsonResponse('success',array('successData'=>$countries),200);
    }

    /**
     * Get List of all Countries Admin api
     */
    public function countryListAdminAction()
    {
        $em = $this->getDoctrine()->getManager();

        $countries = $em->getRepository('AppBundle:Country')->findAllCountry();

        return $this->_createJsonResponse('success',array('successData'=>$countries),200);
    }

    public function _createJsonResponse($key, $data,$code)
    {
        $serializer = $this->container->get('jms_serializer');
        $json = $serializer->serialize([$key => $data], 'json');
        $response = new Response($json, $code);
        return $response;
    }

}
