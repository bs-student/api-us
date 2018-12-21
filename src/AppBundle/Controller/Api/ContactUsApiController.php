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

class ContactUsApiController extends Controller
{


    /**
     * Send Contact Message
     */
    public function sendMessageAction(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        if(array_key_exists('key',$data)){

            $captchaApiInfo = $this->container->getParameter('google_re_captcha_info');

            $host = $captchaApiInfo['host'];
            $secret = $captchaApiInfo['secret'];

            $url= $host."?secret=".$secret."&response=".$data['key'];

            $mobileDeviceInfo = $this->container->getParameter('mobile_device_config');
            $mobileApiKey = $mobileDeviceInfo['api_key'];

            if(!strcmp($mobileApiKey,$data['key'])){
                $captchaResponse['success']=true;
            }else {

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                $jsonOutput = curl_exec($ch);
                curl_close($ch);

                $captchaResponse = json_decode($jsonOutput, true);
            }
            if($captchaResponse['success']){

                $this->get('fos_user.mailer')->sendContactUsEmail($data);

                return $this->_createJsonResponse('success',array(
                    'successTitle'=>"Your message has been sent",
                    'successDescription'=>"We will contact you as soon as possible"
                ),201);
            }else{
                return $this->_createJsonResponse('error',array(
                    'errorTitle'=>"Emails not Sent",
                    'errorDescription'=>"Captcha was Wrong. Reload and try again."
                ),400);
            }
        }else{
            return $this->_createJsonResponse('error',array(
                'errorTitle'=>"Message not Sent",
                'errorDescription'=>"Sorry we were unable to Send the message. FillUp the form and try again."
            ),400);
        }
    }

    /**
     * Send Mails To Friends
     */
    public function sendMailsToFriendsAction(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        if(array_key_exists('key',$data)){

            $captchaApiInfo = $this->container->getParameter('google_re_captcha_info');

            $host = $captchaApiInfo['host'];
            $secret = $captchaApiInfo['secret'];

            $url= $host."?secret=".$secret."&response=".$data['key'];

            $mobileDeviceInfo = $this->container->getParameter('mobile_device_config');
            $mobileApiKey = $mobileDeviceInfo['api_key'];

            if(!strcmp($mobileApiKey,$data['key'])){
                $captchaResponse['success']=true;
            }else {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                $jsonOutput = curl_exec($ch);
                curl_close($ch);

                $captchaResponse = json_decode($jsonOutput,true);
            }

            if($captchaResponse['success']){

                $this->get('fos_user.mailer')->sendFriendsEmail($data);

                return $this->_createJsonResponse('success',array(
                    'successTitle'=>"Emails have successfully sent to your Friends",
                    'successDescription'=>"Thank you for sharing our website."
                ),201);
            }else{
                return $this->_createJsonResponse('error',array(
                    'errorTitle'=>"Emails not Sent",
                    'errorDescription'=>"Captcha was Wrong. Reload and try again."
                ),400);
            }

        }else{
            return $this->_createJsonResponse('error',array(
                'errorTitle'=>"Emails not Sent",
                'errorDescription'=>"Sorry we were unable to Send the Emails. FillUp the form and try again."
            ),400);
        }



    }

    /**
     * Send Mails To Friends of a User
     */

    public function sendMailsToUserFriendsAction(Request $request){
        $data = json_decode($request->getContent(), true);

        $user = $this->get('security.token_storage')->getToken()->getUser();

        if($user instanceof User){
            $data['fullName']=$user->getFullName();
            $data['email']=$user->getEmail();
            $data['username']=$user->getUsername();

            $this->get('fos_user.mailer')->sendShareSellPageEmailToFriends($data);

            $logData = array(
                'user'=>$user->getId(),
                'logType'=>"Promote Sell Page",
                'logDateTime'=>gmdate('Y-m-d H:i:s'),
                'logDescription'=> $user->getUsername()." has promoted own Sell Page",
                'userIpAddress'=>$this->container->get('request')->getClientIp(),
                'logUserType'=> in_array("ROLE_ADMIN_USER",$user->getRoles())?"Admin User":"Normal User"
            );
            $this->_saveLog($logData);

            return $this->_createJsonResponse('success',array(
                'successTitle'=>"Emails have successfully sent to your Friends",
                'successDescription'=>"Thank you for sharing your sell page."
            ),200);
        }else{
            return $this->_createJsonResponse('error',array(
                'errorTitle'=>"Emails couldn't be sent",
                'errorDescription'=>"Reload the page and try again."
            ),201);
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
