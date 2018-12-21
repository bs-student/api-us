<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Log;
use AppBundle\Form\Type\LogType;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Controller\ChangePasswordController as BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller managing the password change
 *
 */
class ChangePasswordController extends BaseController
{

    /**
     * Change password
     */
    public function changePasswordAction()
    {

        $user = $this->container->get('security.context')->getToken()->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            $data = array(
                'errorTitle'=>"Access Denied",
                "errorDescription"=>"Sorry, This user does not have access to this section."
            );
            return $this->_createJsonResponse('error',$data,400);

        }

        $form = $this->container->get('fos_user.change_password.form');
        $formHandler = $this->container->get('fos_user.change_password.form.handler');

        $process = $formHandler->process($user);
        if ($process) {

            $logData = array(
                'user'=>$user->getId(),
                'logType'=>"Change Password",
                'logDateTime'=>gmdate('Y-m-d H:i:s'),
                'logDescription'=> $user->getUsername()." has changed password",
                'userIpAddress'=>$this->container->get('request')->getClientIp(),
                'logUserType'=> in_array("ROLE_ADMIN_USER",$user->getRoles())?"Admin User":"Normal User"
            );
            $this->_saveLog($logData);

            $data = array(
                'successTitle'=>"Password Changed",
                "successDescription"=>"Password is successfully changed."
            );
            return $this->_createJsonResponse('success',$data,200);

        }else{

            $data = array(
                'errorTitle'=>"Sorry, Password could not be changed"
            );
            return $this->_createJsonResponse('error',$data,400);
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

    public function _createJsonResponse($key,$data,$code){
        $serializer = $this->container->get('jms_serializer');
        $json = $serializer->serialize([$key => $data], 'json');
        $response = new Response($json, $code);
        return $response;
    }

}
