<?php

namespace AppBundle\Controller\Api;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\Referral;
use AppBundle\Form\ReferralType;
use Symfony\Component\HttpFoundation\Response;

/**
 * Referral controller.
 *
 */
class ReferralManagementApiController extends Controller
{

    /**
     * All Referrals
     */
    public function referralListAction()
    {
        $em = $this->getDoctrine()->getManager();

        $referrals = $em->getRepository('AppBundle:Referral')->findAll();

        return $this->_createJsonResponse('success',array('successData'=>$referrals),200);

    }

    /**
     * All Referrals Admin
     */
    public function referralListAdminAction()
    {
        $em = $this->getDoctrine()->getManager();

        $referrals = $em->getRepository('AppBundle:Referral')->findAll();

        return $this->_createJsonResponse('success',array('successData'=>$referrals),200);

    }

    public function _createJsonResponse($key, $data,$code)
    {
        $serializer = $this->container->get('jms_serializer');
        $json = $serializer->serialize([$key => $data], 'json');
        $response = new Response($json, $code);
        return $response;
    }

}
