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


class AdminDatabaseApiController extends Controller
{


    /**
     * Get Databases
     *
     */
    public function getAllDatabasesAction(Request $request){
        $user = $this->container->get('security.token_storage')->getToken()->getUser();

        if(in_array('ROLE_ADMIN_USER',$user->getRoles(),true)){
            $fileDirHost = $this->container->getParameter('kernel.root_dir');
            $fileDir = '/../data/databaseBackup/';
            $content = $request->getContent();
            $data = json_decode($content, true);
            $pageSize = $data["pageSize"];
            $pageNumber = $data["pageNumber"];

            $fi = new \FilesystemIterator($fileDirHost.$fileDir, \FilesystemIterator::SKIP_DOTS);

            $databaseListArray=array();
            foreach($fi as $file)
            {
                $databaseListArray[filectime($file)]=array(
                    'fileName' => $file->getFilename(),
                    'fileSize' =>round((floatval(filesize($file)/1024)),2) ." KB",
                    'fileTime'=>date('g:i A, d M Y',filectime($file))
                );
            }
            krsort($databaseListArray);

            $selectedFiles = array_slice($databaseListArray,$pageNumber-1,$pageSize);

            $totalNumber = iterator_count($fi);

            $data = array(
                'totalDatabaseList' => $selectedFiles,
                'totalNumber' => $totalNumber
            );

            return $this->_createJsonResponse('success', array('successData'=>array('databaseList'=>$data)), 200);
        }else{
            return $this->_createJsonResponse('error', array('errorTitle'=>"You are not authorized to see this page."), 400);
        }
    }

    /**
     * Download Database Backup
     *
     */
    public function downloadDatabasesAction(Request $request){
        $user = $this->container->get('security.token_storage')->getToken()->getUser();

        if(in_array('ROLE_ADMIN_USER',$user->getRoles(),true)){
            $fileDirHost = $this->container->getParameter('kernel.root_dir');
            $sourceFileDir = '/../data/databaseBackup/';
            $destinationFileDir = '/../web/databaseBackup/';
            $content = $request->getContent();
            $data = json_decode($content, true);

            copy($fileDirHost.$sourceFileDir.$data['databaseName'],$fileDirHost.$destinationFileDir.$data['databaseName']);

            return $this->_createJsonResponse('success', array('successTitle'=>"Database is Generated",'successData'=>array("link"=>"/databaseBackup/".$data['databaseName'])), 200);
        }else{
            return $this->_createJsonResponse('error', array('errorTitle'=>"You are not authorized to see this page."), 400);
        }
    }

    public function clearPublicDatabaseDirectoryAction(Request $request){
        $fileDirHost = $this->container->getParameter('kernel.root_dir');
        $sourceFileDir = '/../web/databaseBackup/';
        array_map('unlink', glob($fileDirHost.$sourceFileDir."*"));
        return $this->_createJsonResponse('success', array('successTitle'=>"Public Database Directory is Cleared"), 200);
    }

    public function _createJsonResponse($key, $data,$code)
    {
        $serializer = $this->container->get('jms_serializer');
        $json = $serializer->serialize([$key => $data], 'json');
        $response = new Response($json, $code);
        return $response;
    }
}
