<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\Book;
use AppBundle\Entity\BookDeal;
use AppBundle\Entity\Campus;
use AppBundle\Entity\Star;
use AppBundle\Entity\WishList;
use AppBundle\Form\Type\StarType;
use AppBundle\Form\Type\BookDealType;
use AppBundle\Form\Type\UniversityType;
use AppBundle\Form\Type\UserType;
use AppBundle\Form\Type\WishListType;
use Doctrine\Common\Collections\ArrayCollection;
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

class StarManagementApiController extends Controller
{

    /**
     * Add textbook deal into Star List
     */
    public function addBookDealToStarListAction(Request $request){

        $em = $this->getDoctrine()->getManager();
        $starRepo = $em->getRepository("AppBundle:Star");

        $content = $request->getContent();
        $data = json_decode($content, true);

        $user = $this->get('security.token_storage')->getToken()->getUser();

        $alreadyInserted = $starRepo->checkIfAlreadyAddedToStarList($user->getId(),$data['bookDealId']);

        if(!$alreadyInserted){
            $star = new Star();
            $starForm = $this->createForm(new StarType(), $star);
            $starForm ->submit(array(
                'user'=>$user->getId(),
                'bookDeal'=>$data['bookDealId'],
            ));

            if ($starForm->isValid()) {
                $em->persist($star);
                $em->flush();
                return $this->_createJsonResponse('success', array("successTitle" => "BookDeal Successfully Starred","successData"=>array("starred"=>true)), 200);
            } else {
                return $this->_createJsonResponse('error', array("errorTitle" => "Couldn't Starred","errorData" => $starForm), 400);
            }
        }else{

            $star = $starRepo->findBy(array('user'=>$user->getId(),'bookDeal'=>$data['bookDealId']));

            if($star[0] instanceof Star){
                $em->remove($star[0]);
                $em->flush();
                return $this->_createJsonResponse('success', array("successTitle" => "BookDeal Successfully Unstarred","successData"=>array("starred"=>false)), 200);
            }else{
                return $this->_createJsonResponse('error', array("errorTitle" => "Couldn't Unstar"), 400);
            }

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
