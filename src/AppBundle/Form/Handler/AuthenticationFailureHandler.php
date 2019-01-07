<?php
/**
 * Created by PhpStorm.
 * User: ManiaC
 * Date: 8/21/17
 * Time: 9:59 AM
 */

namespace AppBundle\Form\Handler;


use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\Serializer;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\SecurityContext;

class AuthenticationFailureHandler implements AuthenticationFailureHandlerInterface{

    protected $entityManager;
    protected $formFactory;
    protected $JMSSerializerBundle;

    public function __construct(EntityManagerInterface $entityManager,FormFactory $formFactory,Serializer $JMSSerializerBundle) {
        $this->entityManager = $entityManager;
        $this->formFactory = $formFactory;
        $this->JMSSerializerBundle = $JMSSerializerBundle;
    }


    public function onAuthenticationFailure(Request $request,  AuthenticationException $exception){

        $error = $exception->getMessage();
        $errorDescKey = "";

        if(!strcmp($error,"User account is disabled.")){
            $errorDescKey ="PLEASE_CHECK_EMAIL_ACTIVATION";
            $error.=" Please Check Your Email for the Activation Link. Please also check spam or junk if you can't find the email.";
        }

        if(!strcmp($error,"Bad credentials.")){
            $error="The Username or Password you entered is incorrect";
            $errorDescKey = "USER_NAME_OR_EMAIL_ENTERED_INCORRECT";
        }

        return $this->_createJsonResponse('error',array(
            "errorTitle" => "Login Unsuccessful",
            "errorDescription" => $error,
            "errorTitleKey" => "LOGIN_UNSUCCESSFUL",
            "errorDescriptionKey"=>$errorDescKey),400);
    }


    public function _createJsonResponse($key, $data, $code)
    {
        $serializer = $this->JMSSerializerBundle;
        $json = $serializer->serialize([$key => $data], 'json');
        $response = new Response($json, $code);
        return $response;
    }

} 