<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\Book;
use AppBundle\Entity\Campus;
use AppBundle\Entity\Contact;
use AppBundle\Entity\Log;
use AppBundle\Entity\Quote;
use AppBundle\Form\Type\BookDealType;
use AppBundle\Form\Type\ContactType;
use AppBundle\Form\Type\LogType;
use AppBundle\Form\Type\QuoteType;
use AppBundle\Form\Type\UniversityType;
use Doctrine\Common\Collections\ArrayCollection;
use FOS\UserBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Form\Type\CampusType;
use AppBundle\Entity\University;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Form\Type\BookType;
use Symfony\Component\HttpFoundation\FileBag;

class AdminQuoteApiController extends Controller
{


    /**
     * Get Student Quotes
     */
    public function getStudentQuotesAction(Request $request){
        $user = $this->container->get('security.token_storage')->getToken()->getUser();

        if(in_array('ROLE_ADMIN_USER',$user->getRoles(),true)){

            $content = $request->getContent();
            $data = json_decode($content, true);
            $em = $this->getDoctrine()->getManager();
            $quoteRepo=$em->getRepository('AppBundle:Quote');

            $pageSize = $data["pageSize"];
            $searchQuery = filter_var($data["searchQuery"], FILTER_SANITIZE_STRING);
            $pageNumber = $data["pageNumber"];
            $sort = $data["sort"];

            $totalNumber = $quoteRepo->getAllStudentQuoteSearchNumber($searchQuery);
            $searchResults= $quoteRepo->getAllStudentQuoteSearchResult($searchQuery, $pageNumber, $pageSize,$sort);


            $data = array(
                'totalQuotes' => $searchResults ,
                'totalNumber' => $totalNumber
            );

            return $this->_createJsonResponse('success', array('successData'=>array('quotes'=>$data)), 200);
        }else{
            return $this->_createJsonResponse('error', array('errorTitle'=>"You are not authorized to see this page."), 400);
        }
    }

    /**
     * Get University Quote
     */
    public function getUniversityQuotesAction(Request $request){
        $user = $this->container->get('security.token_storage')->getToken()->getUser();

        if(in_array('ROLE_ADMIN_USER',$user->getRoles(),true)){

            $content = $request->getContent();
            $data = json_decode($content, true);
            $em = $this->getDoctrine()->getManager();
            $quoteRepo=$em->getRepository('AppBundle:Quote');

            $pageSize = $data["pageSize"];
            $searchQuery = filter_var($data["searchQuery"], FILTER_SANITIZE_STRING);
            $pageNumber = $data["pageNumber"];
            $sort = $data["sort"];

            $totalNumber = $quoteRepo->getAllUniversityQuoteSearchNumber($searchQuery);
            $searchResults= $quoteRepo->getAllUniversityQuoteSearchResult($searchQuery, $pageNumber, $pageSize,$sort);


            $data = array(
                'totalQuotes' => $searchResults ,
                'totalNumber' => $totalNumber
            );

            return $this->_createJsonResponse('success', array('successData'=>array('quotes'=>$data)), 200);
        }else{
            return $this->_createJsonResponse('error', array('errorTitle'=>"You are not authorized to see this page."), 400);
        }
    }

    /**
     * Update Quote
     */
    public function updateQuoteAction(Request $request){
        $user = $this->container->get('security.token_storage')->getToken()->getUser();

        if(in_array('ROLE_ADMIN_USER',$user->getRoles(),true)){

            $content = $request->getContent();
            $data = json_decode($content, true);
            $em = $this->getDoctrine()->getManager();
            $quoteRepo=$em->getRepository('AppBundle:Quote');

            $quote = $quoteRepo->findOneById($data['quoteId']);

            if($quote!=null){
                $quoteForm = $this->createForm(new QuoteType(), $quote);
                $quoteForm->remove('quoteType');
                $quoteForm->remove('quoteImage');
                $quoteForm->submit($data);

                if ($quoteForm->isValid()) {
                    $em->persist($quote);
                    $em->flush();

                    $logData = array(
                        'user'=>$user->getId(),
                        'logType'=>"Update Quote",
                        'logDateTime'=>gmdate('Y-m-d H:i:s'),
                        'logDescription'=> $quote->getQuoteType()=="Student"?$user->getUsername()." has updated a student quote":$user->getUsername()." has updated an university quote",
                        'userIpAddress'=>$this->container->get('request')->getClientIp(),
                        'logUserType'=> in_array("ROLE_ADMIN_USER",$this->get('security.token_storage')->getToken()->getUser()->getRoles())?"Admin User":"Normal User"
                    );
                    $this->_saveLog($logData);

                    return $this->_createJsonResponse('success', array(
                        'successTitle' => "Quote has been updated"
                    ), 200);
                } else {
                    return $this->_createJsonResponse('error', array("errorTitle"=>"Could Not update quote","errorData" => $quoteForm), 400);
                }
            }else{
                return $this->_createJsonResponse('error', array("errorTitle"=>"Could was not found"), 400);
            }


        }else{
            return $this->_createJsonResponse('error', array('errorTitle'=>"You are not authorized to see this page."), 400);
        }
    }

    /**
     * Add Quote
     */
    public function addQuoteAction(Request $request){
        $user = $this->container->get('security.token_storage')->getToken()->getUser();

        if(in_array('ROLE_ADMIN_USER',$user->getRoles(),true)){

            $content = $request->get('quote');
            $data = json_decode($content, true);
            $em = $this->getDoctrine()->getManager();

            //Prepare File
            $fileDirHost = $this->container->getParameter('kernel.root_dir');
            $fileDir = '/../web/quoteImages/';
            $fileNameDir = '/quoteImages/';
            $files = $request->files;

            //Return Error if image not found
            if(count($files)==0){
                return $this->_createJsonResponse('error', array('errorTitle' => "Cannot Add Quote", 'errorDescription' => "Image not Found"), 400);
            }

            //Upload Image
            $fileUploadError = false;
            foreach ($files as $file) {
                if ((($file->getSize()) / 1024) <= 200) {
                    $fileSaveName = gmdate("Y-d-m_h_i_s_") . rand(0, 99999999) . "." . pathinfo($file->getClientOriginalName())['extension'];
                    $file->move($fileDirHost . $fileDir, $fileSaveName);

                    if(!strcmp('Student',$data['quoteType'])){
                        $this->_smart_resize_image($fileDirHost.$fileDir.$fileSaveName , null, 64 , 64 , false , $fileDirHost.$fileDir.$fileSaveName , false , false ,100 );
                    }elseif(!strcmp('Student',$data['quoteType'])){
                        $this->_smart_resize_image($fileDirHost.$fileDir.$fileSaveName , null, 195 , 195 , false , $fileDirHost.$fileDir.$fileSaveName , false , false ,100 );
                    }


                    $data['quoteImage']= $fileNameDir . $fileSaveName;
                } else {
                    $fileUploadError = true;
                }
            }
            //If Error Occurs than Return Error Message
            if($fileUploadError)return $this->_createJsonResponse('error', array('errorTitle' => "Cannot Add Quote", 'errorDescription' => "Image is more than 200 KB"), 400);



            $quote = new Quote();

            $data['quoteStatus']='Activated';


            $quoteForm = $this->createForm(new QuoteType(), $quote);

            $quoteForm->submit($data);

            if ($quoteForm->isValid()) {
                $em->persist($quote);
                $em->flush();

                $logData = array(
                    'user'=>$user->getId(),
                    'logType'=>"Add Quote",
                    'logDateTime'=>gmdate('Y-m-d H:i:s'),
                    'logDescription'=> $quote->getQuoteType()=="Student"?$user->getUsername()." has added a student quote":$user->getUsername()." has added an university quote",
                    'userIpAddress'=>$this->container->get('request')->getClientIp(),
                    'logUserType'=> in_array("ROLE_ADMIN_USER",$this->get('security.token_storage')->getToken()->getUser()->getRoles())?"Admin User":"Normal User"
                );
                $this->_saveLog($logData);

                return $this->_createJsonResponse('success', array(
                    'successTitle' => "Quote has been created",
                    'successData'=>array(
                        'quoteId'=>$quote->getId(),
                        'quoteImage'=>$quote->getQuoteImage(),
                        'quoteProvider'=>$quote->getQuoteProvider(),
                        'quoteDescription'=>$quote->getQuoteDescription(),
                        'quoteStatus'=>$quote->getQuoteStatus()
                    )
                ), 201);

            } else {
                return $this->_createJsonResponse('error', array("errorTitle"=>"Could Not create quote","errorData" => $quoteForm), 400);
            }


        }else{
            return $this->_createJsonResponse('error', array('errorTitle'=>"You are not authorized to see this page."), 400);
        }
    }

    public function deleteQuoteAction(Request $request){

        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        if(in_array('ROLE_ADMIN_USER',$user->getRoles(),true)){
            $content = $request->getContent();
            $data = json_decode($content, true);
            $em = $this->getDoctrine()->getManager();
            $quoteRepo=$em->getRepository('AppBundle:Quote');

            $quote = $quoteRepo->findOneById($data['quoteId']);

            if($quote!=null){

                $em->remove($quote);
                $em->flush();

                $logData = array(
                    'user'=>$user->getId(),
                    'logType'=>"Delete Quote",
                    'logDateTime'=>gmdate('Y-m-d H:i:s'),
                    'logDescription'=> $quote->getQuoteType()=="Student"?$user->getUsername()." has deleted a student quote":$user->getUsername()." has deleted an university quote",
                    'userIpAddress'=>$this->container->get('request')->getClientIp(),
                    'logUserType'=> in_array("ROLE_ADMIN_USER",$this->get('security.token_storage')->getToken()->getUser()->getRoles())?"Admin User":"Normal User"
                );
                $this->_saveLog($logData);

                return $this->_createJsonResponse('success', array(
                    'successTitle' => "Quote has been deleted"
                ), 200);

            }else{
                return $this->_createJsonResponse('error', array("errorTitle"=>"Quote was not found"), 400);
            }
        }else{
            return $this->_createJsonResponse('error', array('errorTitle'=>"You are not authorized to see this page."), 400);
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

    public function _createJsonResponse($key, $data, $code)
    {
        $serializer = $this->container->get('jms_serializer');
        $json = $serializer->serialize([$key => $data], 'json');
        $response = new Response($json, $code);
        return $response;
    }


}
