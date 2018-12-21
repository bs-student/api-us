<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Log;
use AppBundle\Form\Type\LogType;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccountStatusException;
use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Controller\ResettingController as BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * Controller managing the resetting of the password
 *
 *
 */
class ResettingController extends BaseController
{

    /**
     * Send Email For Resetting Password
     */
    public function sendEmailAction()
    {

        $formHandler = $this->container->get('fos_user.resetting.form.handler');
        $submittedData = $formHandler->getSubmittedData();


        if(array_key_exists('key',$submittedData)){

            $captchaApiInfo = $this->container->getParameter('google_re_captcha_info');

            $host = $captchaApiInfo['host'];
            $secret = $captchaApiInfo['secret'];

            $url= $host."?secret=".$secret."&response=".$submittedData['key'];

            $mobileDeviceInfo = $this->container->getParameter('mobile_device_config');
            $mobileApiKey = $mobileDeviceInfo['api_key'];

            if(!strcmp($mobileApiKey,$submittedData['key'])){
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
                $username = $this->container->get('request')->request->get('username');

                /** @var $user UserInterface */
                $user = $this->container->get('fos_user.user_manager')->findUserByUsernameOrEmail($username);

                if (null === $user) {
                    $data = array(
                        'errorTitle'=>"Cannot Reset Password",
                        "errorDescription"=>"Sorry No User found on that email Address"
                    );
                    return $this->_createJsonResponse('error',$data,400);
                }

                if ($user->getAdminApproved()==="No") {
                    $data = array(
                        'errorTitle'=>"Cannot Reset Password",
                        "errorDescription"=>"Your account has been blocked by Admin. Please contact support or create a new account."
                    );
                    return $this->_createJsonResponse('error',$data,400);
                }

                if ($user->isPasswordRequestNonExpired($this->container->getParameter('fos_user.resetting.token_ttl'))) {
                    $data = array(
                        'errorTitle'=>"Cannot Reset Password",
                        "errorDescription"=>"Sorry the Reset Password was already requested"
                    );
                    return $this->_createJsonResponse('error',$data,400);

                }

                if (null === $user->getConfirmationToken()) {
                    /** @var $tokenGenerator \FOS\UserBundle\Util\TokenGeneratorInterface */
                    $tokenGenerator = $this->container->get('fos_user.util.token_generator');
                    $user->setConfirmationToken($tokenGenerator->generateToken());
                }

                $this->container->get('session')->set(static::SESSION_EMAIL, $this->getObfuscatedEmail($user));
                $this->container->get('fos_user.mailer')->sendResettingEmailMessage($user);
                $user->setPasswordRequestedAt(new \DateTime());
                $this->container->get('fos_user.user_manager')->updateUser($user);

                $data = array(
                    'successTitle'=>"Reset Password Successful",
                    "successDescription"=>"An email has been sent to your email address for resetting password. Please also check spam or junk if you can't find the email."
                );
                return $this->_createJsonResponse('success',$data,200);
            }else{
                return $this->_createJsonResponse('error',array(
                    'errorTitle'=>"Reset Password Unsuccessful",
                    'errorDescription'=>"Captcha was Wrong. Reload and try again."
                ),400);
            }



        }else{
            return $this->_createJsonResponse('error',array(
                'errorTitle'=>"User Registration Unsuccessful",
                'errorDescription'=>"Reload and try again."
            ),400);
        }

    }

    /**
     * Tell the user to check his email provider
     */
    public function checkEmailAction()
    {
        $session = $this->container->get('session');
        $email = $session->get(static::SESSION_EMAIL);
        $session->remove(static::SESSION_EMAIL);

        if (empty($email)) {
            // the user does not come from the sendEmail action
            return new RedirectResponse($this->container->get('router')->generate('fos_user_resetting_request'));
        }

        return $this->container->get('templating')->renderResponse('FOSUserBundle:Resetting:checkEmail.html.'.$this->getEngine(), array(
            'email' => $email,
        ));
    }

    /**
     * Reset Password with a token
     */
    public function resetAction($token=null)
    {

        $user = $this->container->get('fos_user.user_manager')->findUserByConfirmationToken($token);

        if (null === $user) {
            $data = array(
                'errorTitle'=>"Cannot Reset Password",
                "errorDescription"=>"The user with 'confirmation token' does not exist for value '$token'"
            );
            return $this->_createJsonResponse('error',$data,400);
//            throw new NotFoundHttpException(sprintf('The user with "confirmation token" does not exist for value "%s"', $token));
        }

        if (!$user->isPasswordRequestNonExpired($this->container->getParameter('fos_user.resetting.token_ttl'))) {
            return new RedirectResponse($this->container->get('router')->generate('fos_user_resetting_request'));
        }

        $form = $this->container->get('fos_user.resetting.form');
        $formHandler = $this->container->get('fos_user.resetting.form.handler');

        $process = $formHandler->process($user);

        if ($process) {

            $logData = array(
                'user'=>$user->getId(),
                'logType'=>"Reset Password",
                'logDateTime'=>gmdate('Y-m-d H:i:s'),
                'logDescription'=> $user->getUsername()." has Reset the Password",
                'userIpAddress'=>$this->container->get('request')->getClientIp(),
                'logUserType'=> in_array("ROLE_ADMIN_USER",$user->getRoles())?"Admin User":"Normal User"
            );
            $this->_saveLog($logData);

            $this->setFlash('fos_user_success', 'resetting.flash.success');
            $response = new RedirectResponse($this->getRedirectionUrl($user));
            $this->authenticateUser($user, $response);

            $data = array(
                'successTitle'=>"Reset Password Successful",
                "successDescription"=>"Password has been successfully changed"
            );
            return $this->_createJsonResponse('success',$data,200);

        }else{
            $data = array(
                'errorTitle'=>"Cannot Reset Password",
                "errorDescription"=>"Sorry, the password could not be changed"
            );
            return $this->_createJsonResponse('error',$data,400);
        }


    }


    public function checkTokenAction($token=null){
        $user = $this->container->get('fos_user.user_manager')->findUserByConfirmationToken($token);


        if (null === $user) {
            $data = array(
                'errorTitle'=>"The Reset my Password link has already been used or has expired",
            );
            return $this->_createJsonResponse('error',$data,400);
        }

        if (!$user->isPasswordRequestNonExpired($this->container->getParameter('fos_user.resetting.token_ttl'))) {

            $data = array(
                'errorTitle'=>"The Reset my Password link has already been used or has expired",
            );
            return $this->_createJsonResponse('error',$data,400);
        }else{
            $data = array(
                'successData'=>true,
            );
            return $this->_createJsonResponse('success',$data,200);
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
