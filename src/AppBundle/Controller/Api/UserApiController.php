<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\Log;
use AppBundle\Form\Type\EmailNotificationType;
use AppBundle\Form\Type\LogType;
use AppBundle\Form\Type\ProfileType;
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


class UserApiController extends Controller
{


    /**
     * Get Current user Short Details
     */
    public function currentUserShortDetailsAction()
    {
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        if ($user) {
            $user_data = array(
                'username' => $user->getUsername(),
                'fullName' => $user->getFullName(),
                'email' => $user->getEmail(),
                'registrationStatus' => $user->getRegistrationStatus(),
                'userId' => ($user->getGoogleId() != null) ? $user->getGoogleId() : $user->getFacebookId(),
                'campusId' => $user->getCampus()?$user->getCampus()->getId():'',
                'profilePicture' => $user->getProfilePicture(),
                'standardHomePhone' => $user->getStandardHomePhone(),
                'standardCellPhone' => $user->getStandardCellPhone(),
                'standardEmail' => $user->getStandardEmail(),
                'role'=>$user->getRoles(),
                'campusDisplay'=>$user->getCampus()?$user->getCampus()->getUniversity()->getUniversityname().", ".$user->getCampus()->getCampusName().", ".$user->getCampus()->getState()->getStateShortName().", ".$user->getCampus()->getState()->getCountry()->getCountryName():'',
            );


            if(!in_array("ROLE_ADMIN_USER",$user_data['role'])){
                $user_data['universityName'] = $user->getCampus()->getUniversity()->getUniversityName();
            }

            return $this->_createJsonResponse('success',array(
                'successData'=>$user_data,
            ),200);

        }else{
            return $this->_createJsonResponse('error',array(
                'errorTitle'=>"User was not identified",
            ),400);

        }
    }

    /**
     * Get Current user Full Details
     */
    public function currentUserFullDetailsAction()
    {
        $user = $this->container->get('security.context')->getToken()->getUser();
        if ($user) {
            $user_data = array(
                'id' => $user->getId(),
                'username' => $user->getUsername(),
                'fullName' => $user->getFullName(),
                'email' => $user->getEmail(),
                'registrationStatus' => $user->getRegistrationStatus(),
                'userId' => ($user->getGoogleId() != null) ? $user->getGoogleId() : $user->getFacebookId(),
                'campusName' => $user->getCampus()->getCampusName(),
                'campusId' => $user->getCampus()->getId(),
                'universityName' => $user->getCampus()->getUniversity()->getUniversityName(),
                'stateName' => $user->getCampus()->getState()->getStateName(),
                'stateShortName' => $user->getCampus()->getState()->getStateShortName(),
                'countryName' => $user->getCampus()->getState()->getCountry()->getCountryName(),
                'standardHomePhone' => $user->getStandardHomePhone(),
                'standardCellPhone' => $user->getStandardCellPhone(),
                'standardEmail' => $user->getStandardEmail(),
                'role'=>$user->getRoles(),
                'profilePicture' => $user->getProfilePicture(),
                'emailNotification'=>$user->getEmailNotification(),
            );

            return $this->_createJsonResponse('success',array(
                'successData'=>$user_data,
            ),200);

        }else{
            return $this->_createJsonResponse('error',array(
                'errorTitle'=>"User was not identified",
            ),400);
        }

    }


    /**
     * All Users List Admin
     *
     */
    public function adminAllUsersAction()
    {
//        $user = $this->container->get('security.context')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();
        $userRepo = $em->getRepository('AppBundle:User');
        $users = $userRepo->findAllUsers();

        return $this->_createJsonResponse('success',array(
            'successData'=>$users,
        ),200);


    }


    /**
     * Update User Profile
     */
    public function updateUserProfileAction(Request $request){


        $content = $request->get('profileData');
        $data = json_decode($content, true);

        //Prepare File
        $fileDirHost = $this->container->getParameter('kernel.root_dir');
        $fileDir = '/../web/userImages/';
        $fileNameDir = '/userImages/';
        $files = $request->files;

        //Upload Image
        $fileUploadError = false;
        foreach ($files as $file) {
            if ((($file->getSize()) / 1024) <= 200) {
                $fileSaveName = gmdate("Y-d-m_h_i_s_") . rand(0, 99999999) . "." . pathinfo($file->getClientOriginalName())['extension'];
                $file->move($fileDirHost . $fileDir, $fileSaveName);

                $this->_smart_resize_image($fileDirHost.$fileDir.$fileSaveName , null, 200 , 200 , false , $fileDirHost.$fileDir.$fileSaveName , false , false ,100 );

                $data['profilePicture']= $fileNameDir . $fileSaveName;
            } else {
                $fileUploadError = true;
            }
        }

        //If Error Occurs than Return Error Message
        if($fileUploadError)return $this->_createJsonResponse('error', array('errorTitle' => "Cannot Update Profile", 'errorDescription' => "Image is more than 200 KB"), 400);



        $user = $this->container->get('security.context')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();


//        $data = json_decode($request->getContent(), true);

        if ($user != null) {
            $updateForm = $this->createForm(new ProfileType(), $user);

            if(count($files)==0){
                $data['profilePicture']=$user->getProfilePicture();
            }
            $updateForm->submit($data);


            if ($updateForm->isValid()) {
                $em->persist($user);
                $em->flush();

                $userData=array(
                    'campusId'=>$user->getCampus()->getId(),
                    'campusName'=>$user->getCampus()->getCampusName(),
                    'countryName'=>$user->getCampus()->getState()->getCountry()->getCountryName(),
                    'stateName'=>$user->getCampus()->getState()->getStateName(),
                    'stateShortName'=>$user->getCampus()->getState()->getStateShortName(),
                    'universityName'=>$user->getCampus()->getUniversity()->getUniversityName(),
                    'fullName'=>$user->getFullName(),
                    'standardHomePhone'=>$user->getStandardHomePhone(),
                    'standardCellPhone'=>$user->getStandardCellPhone(),
                    'standardEmail'=>$user->getStandardEmail(),
                    'profilePicture'=>$user->getProfilePicture(),
                    'emailNotification'=>$user->getEmailNotification(),

                );

                $logData = array(
                    'user'=>$user->getId(),
                    'logType'=>"Profile Update",
                    'logDateTime'=>gmdate('Y-m-d H:i:s'),
                    'logDescription'=> $user->getUsername()." has updated own profile information.",
                    'userIpAddress'=>$this->container->get('request')->getClientIp(),
                    'logUserType'=> in_array("ROLE_ADMIN_USER",$user->getRoles())?"Admin User":"Normal User"
                );
                $this->_saveLog($logData);

                return $this->_createJsonResponse('success', array('successTitle' => 'Profile is Updated','successData'=>$userData),200);
            } else {
                return $this->_createJsonResponse('error', array(
                    'errorTitle' => 'Full Name is not Updated',
                    'errorDescription' => 'Sorry. Please check the form and submit again.',
                    'errorData'=>$updateForm
                ),400);
            }
        }
    }

    /**
     * Email Notification Update
     */
    public function updateUserEmailNotificationAction(Request $request){


        $content = $request->getContent();
        $data = json_decode($content, true);



        $user = $this->container->get('security.context')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();


        if ($user != null) {
            $updateForm = $this->createForm(new EmailNotificationType(), $user);

            if(!strcmp('On',$data['emailNotification']) || !strcmp('Off',$data['emailNotification']) ){
                $updateForm->submit($data);


                if ($updateForm->isValid()) {
                    $em->persist($user);
                    $em->flush();

                    $userData=array(
                        'emailNotification'=>$user->getEmailNotification()
                    );
                    if(!strcmp("On",$user->getEmailNotification())){
                        $logData = array(
                            'user'=>$user->getId(),
                            'logType'=>"Email Notification Change",
                            'logDateTime'=>gmdate('Y-m-d H:i:s'),
                            'logDescription'=> $user->getUsername()." has turned on Email Notification",
                            'userIpAddress'=>$this->container->get('request')->getClientIp(),
                            'logUserType'=> in_array("ROLE_ADMIN_USER",$user->getRoles())?"Admin User":"Normal User"
                        );
                        $this->_saveLog($logData);
                        return $this->_createJsonResponse('success', array('successTitle' => 'Email Notification is successfully turned on','successData'=>$userData),200);
                    }elseif(!strcmp("Off",$user->getEmailNotification())){
                        $logData = array(
                            'user'=>$user->getId(),
                            'logType'=>"Email Notification Change",
                            'logDateTime'=>gmdate('Y-m-d H:i:s'),
                            'logDescription'=> $user->getUsername()." has turned off Email Notification",
                            'userIpAddress'=>$this->container->get('request')->getClientIp(),
                            'logUserType'=> in_array("ROLE_ADMIN_USER",$user->getRoles())?"Admin User":"Normal User"
                        );
                        $this->_saveLog($logData);
                        return $this->_createJsonResponse('success', array('successTitle' => 'Email Notification is successfully turned off','successData'=>$userData),200);
                    }

                } else {
                    return $this->_createJsonResponse('error', array(
                        'errorTitle' => 'Email Notification Status is not updated',
                        'errorDescription' => 'Sorry. Please check the form and submit again.',
                        'errorData'=>$updateForm
                    ),400);
                }
            }else {
                return $this->_createJsonResponse('error', array(
                    'errorTitle' => 'Email Notification Status is not updated',
                    'errorDescription' => 'Sorry. Please check the form and submit again.',
                    'errorData'=>$updateForm
                ),400);
            }

        }
    }


//Image Resize Function
    function _smart_resize_image($file,
                                 $string             = null,
                                 $width              = 0,
                                 $height             = 0,
                                 $proportional       = false,
                                 $output             = 'file',
                                 $delete_original    = true,
                                 $use_linux_commands = false,
                                 $quality = 100
    ) {

        if ( $height <= 0 && $width <= 0 ) return false;
        if ( $file === null && $string === null ) return false;
        # Setting defaults and meta
        $info                         = $file !== null ? getimagesize($file) : getimagesizefromstring($string);
        $image                        = '';
        $final_width                  = 0;
        $final_height                 = 0;
        list($width_old, $height_old) = $info;
        $cropHeight = $cropWidth = 0;
        # Calculating proportionality
        if ($proportional) {
            if      ($width  == 0)  $factor = $height/$height_old;
            elseif  ($height == 0)  $factor = $width/$width_old;
            else                    $factor = min( $width / $width_old, $height / $height_old );
            $final_width  = round( $width_old * $factor );
            $final_height = round( $height_old * $factor );
        }
        else {
            $final_width = ( $width <= 0 ) ? $width_old : $width;
            $final_height = ( $height <= 0 ) ? $height_old : $height;
            $widthX = $width_old / $width;
            $heightX = $height_old / $height;

            $x = min($widthX, $heightX);
            $cropWidth = ($width_old - $width * $x) / 2;
            $cropHeight = ($height_old - $height * $x) / 2;
        }
        # Loading image to memory according to type
        switch ( $info[2] ) {
            case IMAGETYPE_JPEG:  $file !== null ? $image = imagecreatefromjpeg($file) : $image = imagecreatefromstring($string);  break;
            case IMAGETYPE_GIF:   $file !== null ? $image = imagecreatefromgif($file)  : $image = imagecreatefromstring($string);  break;
            case IMAGETYPE_PNG:   $file !== null ? $image = imagecreatefrompng($file)  : $image = imagecreatefromstring($string);  break;
            default: return false;
        }


        # This is the resizing/resampling/transparency-preserving magic
        $image_resized = imagecreatetruecolor( $final_width, $final_height );
        if ( ($info[2] == IMAGETYPE_GIF) || ($info[2] == IMAGETYPE_PNG) ) {
            $transparency = imagecolortransparent($image);
            $palletsize = imagecolorstotal($image);
            if ($transparency >= 0 && $transparency < $palletsize) {
                $transparent_color  = imagecolorsforindex($image, $transparency);
                $transparency       = imagecolorallocate($image_resized, $transparent_color['red'], $transparent_color['green'], $transparent_color['blue']);
                imagefill($image_resized, 0, 0, $transparency);
                imagecolortransparent($image_resized, $transparency);
            }
            elseif ($info[2] == IMAGETYPE_PNG) {
                imagealphablending($image_resized, false);
                $color = imagecolorallocatealpha($image_resized, 0, 0, 0, 127);
                imagefill($image_resized, 0, 0, $color);
                imagesavealpha($image_resized, true);
            }
        }
        imagecopyresampled($image_resized, $image, 0, 0, $cropWidth, $cropHeight, $final_width, $final_height, $width_old - 2 * $cropWidth, $height_old - 2 * $cropHeight);


        # Taking care of original, if needed
        if ( $delete_original ) {
            if ( $use_linux_commands ) exec('rm '.$file);
            else @unlink($file);
        }
        # Preparing a method of providing result
        switch ( strtolower($output) ) {
            case 'browser':
                $mime = image_type_to_mime_type($info[2]);
                header("Content-type: $mime");
                $output = NULL;
                break;
            case 'file':
                $output = $file;
                break;
            case 'return':
                return $image_resized;
                break;
            default:
                break;
        }

        # Writing image according to type to the output destination and image quality
        switch ( $info[2] ) {
            case IMAGETYPE_GIF:   imagegif($image_resized, $output);    break;
            case IMAGETYPE_JPEG:  imagejpeg($image_resized, $output, $quality);   break;
            case IMAGETYPE_PNG:
                $quality = 9 - (int)((0.9*$quality)/10.0);
                imagepng($image_resized, $output, $quality);
                break;
            default: return false;
        }
        return true;
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
