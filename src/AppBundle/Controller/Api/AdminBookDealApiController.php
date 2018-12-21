<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\Book;
use AppBundle\Entity\Campus;
use AppBundle\Entity\Contact;
use AppBundle\Form\Type\BookDealType;
use AppBundle\Form\Type\ContactType;
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

class AdminBookDealApiController extends Controller
{


    /**
     * Get All Book Deals
     */
    public function getAllBookDealsAction(Request $request){
        $user = $this->container->get('security.token_storage')->getToken()->getUser();

        if(in_array('ROLE_ADMIN_USER',$user->getRoles(),true)){

            $content = $request->getContent();
            $data = json_decode($content, true);
            $em = $this->getDoctrine()->getManager();
            $bookDealRepo=$em->getRepository('AppBundle:BookDeal');


            $pageSize = $data["pageSize"];
            $searchQuery = filter_var($data["searchQuery"], FILTER_SANITIZE_STRING);
            $pageNumber = $data["pageNumber"];
            $sort = $data["sort"];



            $totalNumber = $bookDealRepo->getAllBookDealSearchNumber($searchQuery);
            $searchResults= $bookDealRepo->getAllBookDealSearchResult($searchQuery, $pageNumber, $pageSize,$sort);


            $books=array();
            foreach ($searchResults as $deal) {


                //Setup Title
                if (strpos($deal['bookTitle'], ":")) {
                    $deal['bookSubTitle'] = substr($deal['bookTitle'], strpos($deal['bookTitle'], ":") + 2);
                    $deal['bookTitle'] = substr($deal['bookTitle'], 0, strpos($deal['bookTitle'], ":"));
                }

                //Formatting Date
                if (array_key_exists('bookPublishDate', $deal)) {
                    $deal['bookPublishDate'] = $deal['bookPublishDate']->format('d M Y');
                }
                if ($deal['bookAvailableDate'] != null) {
                    $deal['bookAvailableDate'] = $deal['bookAvailableDate']->format('d M Y');
                }

                //Getting Images
                $images = array();
                $bookDeal = $bookDealRepo->findOneById($deal['bookDealId']);
                //GET FIRST IMAGE OF THAT BOOK
                array_push($images,array(
                    'image'=>$deal['bookImage'],
                    'imageId'=>0
                ));

                $bookDealImages = $bookDeal->getBookDealImages();
                for($i=0;$i<count($bookDealImages);$i++){
                    array_push($images,array(
                        'image'=>$bookDealImages[$i]->getImageUrl(),
                        'imageId'=>($i+1)
                    ));
                }
                $deal['bookImages']=$images;
                array_push($books,$deal);

            }



            $data = array(
                'totalBooks' => $books ,
                'totalNumber' => $totalNumber
            );

            return $this->_createJsonResponse('success', array('successData'=>array('books'=>$data)), 200);
        }else{
            return $this->_createJsonResponse('error', array('errorTitle'=>"You are not authorized to see this page."), 400);
        }
    }

    /**
     * Get All Sold Book Deals
     */
    public function getAllSoldBookDealsAction(Request $request){
        $user = $this->container->get('security.token_storage')->getToken()->getUser();

        if(in_array('ROLE_ADMIN_USER',$user->getRoles(),true)){

            $content = $request->getContent();
            $data = json_decode($content, true);
            $em = $this->getDoctrine()->getManager();
            $bookDealRepo=$em->getRepository('AppBundle:BookDeal');


            $pageSize = $data["pageSize"];
            $searchQuery = filter_var($data["searchQuery"], FILTER_SANITIZE_STRING);
            $pageNumber = $data["pageNumber"];
            $sort = $data["sort"];



            $totalNumber = $bookDealRepo->getAllSoldBookDealSearchNumber($searchQuery);
            $searchResults= $bookDealRepo->getAllSoldBookDealSearchResult($searchQuery, $pageNumber, $pageSize,$sort);


            $books=array();
            foreach ($searchResults as $deal) {


                //Setup Title
                if (strpos($deal['bookTitle'], ":")) {
                    $deal['bookSubTitle'] = substr($deal['bookTitle'], strpos($deal['bookTitle'], ":") + 2);
                    $deal['bookTitle'] = substr($deal['bookTitle'], 0, strpos($deal['bookTitle'], ":"));
                }

                //Formatting Date
                if (array_key_exists('bookPublishDate', $deal)) {
                    $deal['bookPublishDate'] = $deal['bookPublishDate']->format('d M Y');
                }
                if ($deal['bookAvailableDate'] != null) {
                    $deal['bookAvailableDate'] = $deal['bookAvailableDate']->format('d M Y');
                }

                //Getting Images
                $images = array();
                $bookDeal = $bookDealRepo->findOneById($deal['bookDealId']);
                //GET FIRST IMAGE OF THAT BOOK
                array_push($images,array(
                    'image'=>$deal['bookImage'],
                    'imageId'=>0
                ));

                $bookDealImages = $bookDeal->getBookDealImages();
                for($i=0;$i<count($bookDealImages);$i++){
                    array_push($images,array(
                        'image'=>$bookDealImages[$i]->getImageUrl(),
                        'imageId'=>($i+1)
                    ));
                }
                $deal['bookImages']=$images;
                array_push($books,$deal);

            }



            $data = array(
                'totalBooks' => $books ,
                'totalNumber' => $totalNumber
            );

            return $this->_createJsonResponse('success', array('successData'=>array('books'=>$data)), 200);
        }else{
            return $this->_createJsonResponse('error', array('errorTitle'=>"You are not authorized to see this page."), 400);
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
