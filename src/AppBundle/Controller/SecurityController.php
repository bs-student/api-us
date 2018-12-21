<?php
/**
 * Created by PhpStorm.
 * User: Sujit
 * Date: 1/15/16
 * Time: 6:46 PM
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Log;
use AppBundle\Form\Type\LogType;
use AppBundle\Form\Type\UserType;
use FOS\UserBundle\Controller\SecurityController as BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\SecurityContext;


class SecurityController extends BaseController {

    /**
     *  Show Homepage
     */
    public function indexAction()
    {

        return $this->_createJsonResponse('success',array(
            "successTitle" => "Homepage",
            "successDescription"=> "You have successfully accessed the Web Api"
        ),200);
    }

    /**
     *  Show Login Page & Show Errors too
     *
     */
    public function loginAction()
    {
        return $this->_createJsonResponse('error',array("errorTitle" => "Redirect To Login Page", "errorDescription" => "Please Try to Login Again"),400);
    }


    public function _createJsonResponse($key, $data, $code)
    {
        $serializer = $this->container->get('jms_serializer');
        $json = $serializer->serialize([$key => $data], 'json');
        $response = new Response($json, $code);
        return $response;
    }


} 