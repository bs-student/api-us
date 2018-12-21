<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\Book;
use AppBundle\Entity\BookDeal;
use AppBundle\Entity\Campus;
use AppBundle\Entity\WishList;
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

class WishListManagementApiController extends Controller
{

    /**
     * Add book into WishList
     */
    public function addBookToWishListAction(Request $request){

        $em = $this->getDoctrine()->getManager();
        $wishListRepo = $em->getRepository("AppBundle:WishList");

        $content = $request->getContent();
        $data = json_decode($content, true);

        $user = $this->get('security.token_storage')->getToken()->getUser();


        $alreadyInserted = $wishListRepo->checkIfAlreadyAddedToWishList($user->getId(),$data['bookId']);


        if($alreadyInserted instanceof WishList){
            $em->remove($alreadyInserted);
            try {
                $em->flush();
                return $this->_createJsonResponse('success',array('successTitle'=>"Wish List Item has been removed"),200);
            }catch (Exception $e){
                return $this->_createJsonResponse('error',array('errorTitle'=>"Wish List Item could not be removed"),400);
            }
        }else{
            $wishList = new WishList();
            $wishListForm = $this->createForm(new WishListType(), $wishList);
            $wishListForm->submit(array(
                'user'=>$user->getId(),
                'book'=>$data['bookId'],
            ));

            if ($wishListForm->isValid()) {
                $em->persist($wishList);
                $em->flush();
                return $this->_createJsonResponse('success', array("successTitle" => "Book Successfully Added to WishList"), 200);
            } else {
                return $this->_createJsonResponse('error', array("errorTitle" => "Couldn't Added to Wishlist","errorData" => $wishListForm), 400);
            }
        }




    }

    /**
     * GET My Wishlist
     */
    public function getMyWishListAction(Request $request){

        $user = $this->get('security.token_storage')->getToken()->getUser();

        $wishListBooks = $user->getWishLists();

        $books=array();
        foreach($wishListBooks as $row){
            $bookEntity=$row->getBook();
            $book=array();

            $book['bookId']=$bookEntity->getId();
            $book['bookTitle']=$bookEntity->getBookTitle();
            $book['bookDirectorAuthorArtist']=$bookEntity->getBookDirectorAuthorArtist();
            $book['bookEdition']=$bookEntity->getBookEdition();
            $book['bookPublisher']=$bookEntity->getBookPublisher();
            $book['bookPublishDate']=$bookEntity->getBookPublishDate();
            $book['bookBinding']=$bookEntity->getBookBinding();
            $book['bookPage']=$bookEntity->getBookPage();
            $book['bookLanguage']=$bookEntity->getBooklanguage();
            $book['bookDescription']=$bookEntity->getBookDescription();
            $book['bookIsbn10']=$bookEntity->getBookIsbn10();
            $book['bookIsbn13']=$bookEntity->getBookIsbn13();
            $book['bookImage']=$bookEntity->getBookImage();
            $book['bookAmazonPrice']="$".$bookEntity->getBookAmazonPrice();


            //Formatting Date
            if ($book['bookPublishDate']!=null) {
                $book['bookPublishDate'] = $book['bookPublishDate']->format('d M Y');
            }

            //Getting Images

            //GET FIRST IMAGE OF THAT BOOK
            $book['bookImages'] = array();

            $image = array(
                'image'=>$book['bookImage'],
                'imageId'=>0
            );
            array_push($book['bookImages'],$image);

            //Formatting Title & SubTitle
            if (strpos($book['bookTitle'], ":")) {
                $book['bookSubTitle'] = substr($book['bookTitle'], strpos($book['bookTitle'], ":") + 2);
                $book['bookTitle'] = substr($book['bookTitle'], 0, strpos($book['bookTitle'], ":"));
            }

            array_push($books,$book);

        }

        return $this->_createJsonResponse('success',array('successData'=>$books),200);



    }

    /**
     * Remove My Wishlist Item
     */
    public function removeWishListItemAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $wishListRepo = $em->getRepository("AppBundle:WishList");

        $content = $request->getContent();
        $data = json_decode($content, true);

        $user = $this->get('security.token_storage')->getToken()->getUser();

        $data = $wishListRepo->findBy(array('book'=>$data['bookId'],'user'=>$user->getId()));

        if($data==null){
            return $this->_createJsonResponse('error',array('errorTitle'=>"Sorry, Can't Delete","errorDescription"=>"This book isn't in your wishlist"),400);
        }

        $em->remove($data[0]);

        try {
            $em->flush();
            return $this->_createJsonResponse('success',array('successTitle'=>"Wish List Item has been removed"),200);
        }catch (Exception $e){
            return $this->_createJsonResponse('error',array('errorTitle'=>"Wish List Item could not be removed"),400);
        }


    }

    /**
     * Check If Added into wishlist
     */
    public function checkIfAddedIntoWishlistAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $wishListRepo = $em->getRepository("AppBundle:WishList");
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $content = $request->getContent();
        $data = json_decode($content, true);

        $wishListData= $wishListRepo->checkIfBookAlreadyAddedByIsbn($user->getId(),$data['isbn']);
        return $this->_createJsonResponse('success',array('successData'=>$wishListData),200);

    }


    public function _createJsonResponse($key, $data, $code)
    {
        $serializer = $this->container->get('jms_serializer');
        $json = $serializer->serialize([$key => $data], 'json');
        $response = new Response($json, $code);
        return $response;
    }

}
