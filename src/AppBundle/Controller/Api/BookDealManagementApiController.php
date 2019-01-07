<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\Book;
use AppBundle\Entity\Campus;
use AppBundle\Entity\Contact;
use AppBundle\Entity\Log;
use AppBundle\Form\Type\BookDealType;
use AppBundle\Form\Type\ContactType;
use AppBundle\Form\Type\LogType;
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

class BookDealManagementApiController extends Controller
{


    /**
     * Get Books I Have Contacted For
     */
    public function getBooksIHaveContactedForAction(Request $request)
    {

        $content = $request->getContent();
        $data = json_decode($content, true);

        $deals = array();

        $userId = $this->get('security.token_storage')->getToken()->getUser()->getId();
        $em = $this->getDoctrine()->getManager();
        $bookDealRepo = $em->getRepository('AppBundle:BookDeal');
        $userRepo = $em->getRepository('AppBundle:User');
        $bookDeals = $bookDealRepo->getBooksIHaveContactedFor($userId,$data['pageNumber'],$data['pageSize']);
        $bookDealsNumber = $bookDealRepo->getBooksIHaveContactedForTotalNumber($userId);

        //Set Subtitle in Book
        for ($i = 0; $i < count($bookDeals); $i++) {
            $bookDeals[$i]['contacts'] = array();
            if (strpos($bookDeals[$i]['bookTitle'], ":")) {
                $bookDeals[$i]['bookSubTitle'] = substr($bookDeals[$i]['bookTitle'], strpos($bookDeals[$i]['bookTitle'], ":") + 2);
                $bookDeals[$i]['bookTitle'] = substr($bookDeals[$i]['bookTitle'], 0, strpos($bookDeals[$i]['bookTitle'], ":"));
            }

        }

        foreach ($bookDeals as $deal) {


            //Formatting Date
            if (array_key_exists('bookPublishDate', $deal)) {
                $deal['bookPublishDate'] = $deal['bookPublishDate']->format('d M Y');
            }
            if ($deal['bookAvailableDate'] != null) {
                $deal['bookAvailableDate'] = $deal['bookAvailableDate']->format('d M Y');
            }

            if ($deal['contactDateTime'] != null) {
                $deal['contactDateTime'] = $deal['contactDateTime']->format('d M Y');
            }

            //Formatting Contact
            array_push($deal['contacts'],array(
                'contactDateTime'=>$deal['contactDateTime'],
                'contactId' =>$deal['contactId']
            ));



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

            array_push($deals,$deal);

        }


        return $this->_createJsonResponse('success', array(
            'successData' => array(
                'result'=>$deals,
                'totalNumber'=>$bookDealsNumber
            )
        ), 200);
    }

    /**
     * Get Books I Have Created For
     */
    public function getBooksIHaveCreatedAction(Request $request)
    {
        $content = $request->getContent();
        $data = json_decode($content, true);

        $deals=array();

        $userId = $this->get('security.token_storage')->getToken()->getUser()->getId();
        $em = $this->getDoctrine()->getManager();
        $bookDealRepo = $em->getRepository('AppBundle:BookDeal');
        $userRepo = $em->getRepository('AppBundle:User');
        $bookDeals = $bookDealRepo->getBooksIHaveCreated($userId,$data['pageNumber'],$data['pageSize']);
        $bookDealsNumber = $bookDealRepo->getBooksIHaveCreatedTotalNumber($userId);

        //Getting Contacts of Deals
        $contacts = $bookDealRepo->getContactsOfBookDeals($bookDeals);


        //Set Subtitle in Book
        for ($i = 0; $i < count($bookDeals); $i++) {
            $bookDeals[$i]['contacts'] = array();
            if (strpos($bookDeals[$i]['bookTitle'], ":")) {
                $bookDeals[$i]['bookSubTitle'] = substr($bookDeals[$i]['bookTitle'], strpos($bookDeals[$i]['bookTitle'], ":") + 2);
                $bookDeals[$i]['bookTitle'] = substr($bookDeals[$i]['bookTitle'], 0, strpos($bookDeals[$i]['bookTitle'], ":"));
            }

        }

        //Adding Contacts according to deals
        if($contacts==null){
            $contacts=array();
        }
        foreach ($contacts as $contact) {

            for ($i = 0; $i < count($bookDeals); $i++) {
                if ((int)$contact['bookDealId'] == (int)$bookDeals[$i]['bookDealId']) {

                    if ($contact['buyerNickName'] == null) {
                        $user = $userRepo->findById((int)$contact['buyerId']);
                        $contact['buyerNickName'] = $user[0]->getUsername();
                    }
                    $contact['contactDateTime'] = $contact['contactDateTime']->format('H:i, d-M-Y');
                    array_push($bookDeals[$i]['contacts'], $contact);
                }
            }

        }

        //Getting Deals I have created
        foreach ($bookDeals as $deal) {

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


            array_push($deals,$deal);


        }

        return $this->_createJsonResponse('success', array(
            'successData' => array(
                'result'=>$deals,
                'totalNumber'=>$bookDealsNumber
            )
        ), 200);
    }

    /**
     * Sell Book to A User
     */
    public function sellBookToUserAction(Request $request)
    {
        $content = $request->getContent();
        $data = json_decode($content, true);
        $em = $this->getDoctrine()->getManager();
        $contactRepo = $em->getRepository('AppBundle:Contact');
        $userId = $this->get('security.token_storage')->getToken()->getUser()->getId();

        //Check If contact Id exist
        if (array_key_exists('contactId', $data)) {

            $contact = $contactRepo->findOneById($data['contactId']);

            if($contact instanceof Contact){

                $bookDeal = $contact->getBookDeal();

                //IF User is the owner of that deal and deal is activated
                if ($bookDeal->getSeller()->getId() == $userId && (!strcmp($bookDeal->getBookStatus(),'Activated'))) {

                    $bookDealData=array(
                        'bookSellingStatus'=>"Sold"
                    );

                    if (($contact->getBuyer() instanceof User)) {
                        //Sell the book by buyer Id
                        $bookDealData['buyer']=$contact->getBuyer()->getId();
                        $buyerName = $contact->getBuyer()->getusername();
                    }elseif($contact->getBuyer()==null){
                        $buyerName = $contact->getBuyerNickName();
                    }

                    // Update Book Deal
                    $bookDealForm = $this->createForm(new BookDealType(), $bookDeal);
                    $bookDealForm->remove('book');
                    $bookDealForm->remove('bookPriceSell');
                    $bookDealForm->remove('bookCondition');
                    $bookDealForm->remove('bookIsHighlighted');
                    $bookDealForm->remove('bookHasNotes');
                    $bookDealForm->remove('bookComment');
                    $bookDealForm->remove('bookContactMethod');
                    $bookDealForm->remove('bookContactHomeNumber');
                    $bookDealForm->remove('bookContactCellNumber');
                    $bookDealForm->remove('bookContactEmail');
                    $bookDealForm->remove('bookIsAvailablePublic');
                    $bookDealForm->remove('bookPaymentMethodCashOnExchange');
                    $bookDealForm->remove('bookPaymentMethodCheque');
                    $bookDealForm->remove('bookAvailableDate');
                    $bookDealForm->remove('bookSubmittedDateTime');
                    $bookDealForm->remove('seller');
                    $bookDealForm->remove('bookStatus');
                    $bookDealForm->remove('bookViewCount');
                    $bookDealForm->remove('bookDealImages');

                    $bookDealForm->submit($bookDealData);


                    $contactForm = $this->createForm(new ContactType(), $contact);
                    $contactForm ->remove('buyerNickName');
                    $contactForm ->remove('buyerEmail');
                    $contactForm ->remove('buyerHomePhone');
                    $contactForm ->remove('buyerCellPhone');
                    $contactForm ->remove('bookDeal');
                    $contactForm ->remove('buyer');
                    $contactForm ->remove('messages');
                    $contactForm ->remove('contactDateTime');

                    $contactData=array(
                        'soldToThatBuyer'=>"Yes"
                    );

                    $contactForm->submit($contactData);

                    if ($bookDealForm->isValid() && $contactForm->isValid()) {
                        $em->persist($bookDeal);
                        $em->persist($contact);
                        $em->flush();

                        $logData = array(
                            'user'=>$this->get('security.token_storage')->getToken()->getUser()->getId(),
                            'logType'=>"Sold Book",
                            'logDateTime'=>gmdate('Y-m-d H:i:s'),
                            'logDescription'=> $this->get('security.token_storage')->getToken()->getUser()->getUsername()." has sold '".$bookDeal->getBook()->getBookTitle()."' book to buyer named ".$buyerName,
                            'userIpAddress'=>$this->container->get('request')->getClientIp(),
                            'logUserType'=> in_array("ROLE_ADMIN_USER",$this->get('security.token_storage')->getToken()->getUser()->getRoles())?"Admin User":"Normal User"
                        );
                        $this->_saveLog($logData);

                        return $this->_createJsonResponse('success', array(
                            'successTitle' => "Book Sold to ".$buyerName
                        ), 200);
                    } else {
                        return $this->_createJsonResponse('error', array("errorTitle"=>"Could Not Sell The Book","errorData" => array($bookDealForm,$contactForm)), 400);
                    }



                }else{
                    return $this->_createJsonResponse('error',array(
                        'errorTitle'=>'Cannot Sell Book',
                        'errorDescription'=>"You Didn't post that deal or Book is deactivated right now"
                    ),400);
                }
            }else{
                return $this->_createJsonResponse('error',array(
                    'errorTitle'=>'Cannot Sell Book',
                    'errorDescription'=>'Check The Form and Submit Again'
                ),400);
            }

        }else{
            return $this->_createJsonResponse('error',array(
                'errorTitle'=>'Cannot Sell Book',
                'errorDescription'=>'Check The Form and Submit Again'
            ),400);
        }


    }

    /**
     * Get Books I Have Created For and Sold (Sell Archive)
     */
    public function getBooksIHaveCreatedAndSoldAction(Request $request){

        $content = $request->getContent();
        $data = json_decode($content, true);

        $deals = array();

        $userId = $this->get('security.token_storage')->getToken()->getUser()->getId();
        $em = $this->getDoctrine()->getManager();
        $bookDealRepo = $em->getRepository('AppBundle:BookDeal');
        $userRepo = $em->getRepository('AppBundle:User');
        $bookDeals = $bookDealRepo->getBooksIHaveCreatedAndSold($userId,$data['pageNumber'],$data['pageSize']);
        $bookDealsNumber = $bookDealRepo->getBooksIHaveCreatedAndSoldTotalNumber($userId);

        //Getting Contacts of Deals
        $contacts = $bookDealRepo->getContactsOfBookDeals($bookDeals);

        //Set Subtitle in Book
        for ($i = 0; $i < count($bookDeals); $i++) {
            $bookDeals[$i]['contacts'] = array();
            if (strpos($bookDeals[$i]['bookTitle'], ":")) {
                $bookDeals[$i]['bookSubTitle'] = substr($bookDeals[$i]['bookTitle'], strpos($bookDeals[$i]['bookTitle'], ":") + 2);
                $bookDeals[$i]['bookTitle'] = substr($bookDeals[$i]['bookTitle'], 0, strpos($bookDeals[$i]['bookTitle'], ":"));
            }

        }

        //Adding Contacts according to deals
        if($contacts==null){
            $contacts=array();
        }
        foreach ($contacts as $contact) {

            for ($i = 0; $i < count($bookDeals); $i++) {
                if ((int)$contact['bookDealId'] == (int)$bookDeals[$i]['bookDealId']) {

                    if ($contact['buyerNickName'] == null) {
                        $user = $userRepo->findById((int)$contact['buyerId']);
                        $contact['buyerNickName'] = $user[0]->getUsername();
                    }
                    $contact['contactDateTime'] = $contact['contactDateTime']->format('H:i d M Y');
                    array_push($bookDeals[$i]['contacts'], $contact);
                }
            }

        }

        //Getting Deals I have created
        foreach ($bookDeals as $deal) {


            //Getting Buyer
            if($deal['buyerId']!=null){
                $buyer = $userRepo->findOneById($deal['buyerId']);
                $deal['buyerNickName']=$buyer->getUsername();
            }else{
                $buyer = $bookDealRepo->getPublicUserWhoBoughtBookDeal($deal['bookDealId']);
                $deal['buyerNickName']=$buyer[0]['buyerNickName'];

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

            array_push($deals,$deal);


        }

        return $this->_createJsonResponse('success', array(
            'successData' => array(
                'result'=>$deals,
                'totalNumber'=>$bookDealsNumber
            )
        ), 200);
    }

    /**
     * Get Books I Have Have Bought (Buy Archive)
     */
    public function getBooksIHaveBoughtAction(Request $request){

        $content = $request->getContent();
        $data = json_decode($content, true);

        $deals=array();

        $userId = $this->get('security.token_storage')->getToken()->getUser()->getId();
        $em = $this->getDoctrine()->getManager();
        $bookDealRepo = $em->getRepository('AppBundle:BookDeal');
        $userRepo = $em->getRepository('AppBundle:User');
        $bookDeals = $bookDealRepo->getBooksIHaveBought($userId,$data['pageNumber'],$data['pageSize']);
        $bookDealsNumber = $bookDealRepo->getBooksIHaveBoughtTotalNumber($userId);


        //Set Subtitle in Book
        for ($i = 0; $i < count($bookDeals); $i++) {
            $bookDeals[$i]['contacts'] = array();
            if (strpos($bookDeals[$i]['bookTitle'], ":")) {
                $bookDeals[$i]['bookSubTitle'] = substr($bookDeals[$i]['bookTitle'], strpos($bookDeals[$i]['bookTitle'], ":") + 2);
                $bookDeals[$i]['bookTitle'] = substr($bookDeals[$i]['bookTitle'], 0, strpos($bookDeals[$i]['bookTitle'], ":"));
            }

        }

        //Getting Deals I have created
        foreach ($bookDeals as $deal) {


            //Getting Buyer
            if($deal['buyerId']!=null){
                $buyer = $userRepo->findOneById($deal['buyerId']);
                $deal['buyerNickName']=$buyer->getUsername();
            }else{
                $buyer = $bookDealRepo->getPublicUserWhoBoughtBookDeal($deal['bookDealId']);
                $deal['buyerNickName']=$buyer[0]['buyerNickName'];

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

            //Formatting Contact
            array_push($deal['contacts'],array(
                'contactDateTime'=>$deal['contactDateTime'],
                'contactId' =>$deal['contactId']
            ));

            array_push($deals,$deal);


        }


        return $this->_createJsonResponse('success', array(
            'successData' => array(
                'result'=>$deals,
                'totalNumber'=>$bookDealsNumber
            )
        ), 200);
    }

    /**
     * Change Book Deal Status
     */
    public function changeBookDealStatusAction(Request $request){

        $content = $request->getContent();
        $data = json_decode($content, true);

        $userId = $this->get('security.token_storage')->getToken()->getUser()->getId();
        $em = $this->getDoctrine()->getManager();
        $bookDealRepo = $em->getRepository('AppBundle:BookDeal');
        $bookDeal = $bookDealRepo->findOneById($data['bookDealId']);

        if($bookDeal->getSeller()->getId()==$userId){


            $bookDealForm = $this->createForm(new BookDealType(), $bookDeal);
            $bookDealForm->remove('book');
            $bookDealForm->remove('bookPriceSell');
            $bookDealForm->remove('bookCondition');
            $bookDealForm->remove('bookIsHighlighted');
            $bookDealForm->remove('bookHasNotes');
            $bookDealForm->remove('bookComment');
            $bookDealForm->remove('bookContactMethod');
            $bookDealForm->remove('bookContactHomeNumber');
            $bookDealForm->remove('bookContactCellNumber');
            $bookDealForm->remove('bookContactEmail');
            $bookDealForm->remove('bookIsAvailablePublic');
            $bookDealForm->remove('bookPaymentMethodCashOnExchange');
            $bookDealForm->remove('bookPaymentMethodCheque');
            $bookDealForm->remove('bookAvailableDate');
            $bookDealForm->remove('seller');
            $bookDealForm->remove('buyer');
            $bookDealForm->remove('bookSellingStatus');
            $bookDealForm->remove('bookViewCount');
            $bookDealForm->remove('bookDealImages');
            $bookDealForm->remove('bookSubmittedDateTime');

            $bookDealForm->submit($data);

            if ($bookDealForm->isValid() ) {
                $em->persist($bookDeal);

                $em->flush();

                $logData = array(
                    'user'=>$this->get('security.token_storage')->getToken()->getUser()->getId(),
                    'logType'=>"Update Book Deal",
                    'logDateTime'=>gmdate('Y-m-d H:i:s'),
                    'logDescription'=> $data['bookStatus']=="Activated"?$this->get('security.token_storage')->getToken()->getUser()->getUsername()." has activated a book deal":$this->get('security.token_storage')->getToken()->getUser()->getUsername()." has deactivated a book deal",
                    'userIpAddress'=>$this->container->get('request')->getClientIp(),
                    'logUserType'=> in_array("ROLE_ADMIN_USER",$this->get('security.token_storage')->getToken()->getUser()->getRoles())?"Admin User":"Normal User"
                );
                $this->_saveLog($logData);

                return $this->_createJsonResponse('success', array(
                    'successTitle' => "Textbook Deal Successfully Updated"
                ), 200);

            } else {
                return $this->_createJsonResponse('error', array("errorTitle"=>"Could Not Update Book Deal","errorData" => array($bookDealForm)), 400);
            }


        }else{
            $this->_createJsonResponse('error',array('errorTitle'=>"Sorry, You did not post this book."),400);
        }

    }

    /**
     * GTE Lowest Campus Book DEal Price
     */
    function getLowestCampusDealPriceAction(Request $request){
        $content = $request->getContent();
        $data = json_decode($content, true);

        $userCampusId = $this->container->get('security.token_storage')->getToken()->getUser()->getCampus()->getId();
        $em = $this->getDoctrine()->getManager();
        $bookDealRepo=$em->getRepository('AppBundle:BookDeal');
        $lowestPriceOnCampus = $bookDealRepo->getLowestDealPriceInCampus($userCampusId,$data['bookIsbn']);

        if($lowestPriceOnCampus[0][1]!=null){
            return $this->_createJsonResponse('success',array('successData'=>array(
                'lowestCampusPrice'=>"$".number_format(floatval($lowestPriceOnCampus[0][1]),2)
            )),200);
        }else{
            return $this->_createJsonResponse('success',array('successData'=>array()),200);
        }

    }

    /**
     * Update book Deal
     */
    function updateBookDealAction(Request $request){
        $em = $this->getDoctrine()->getManager();

        //Get Image Save Dir
        $fileDirHost = $this->container->getParameter('kernel.root_dir');
        //TODO Fix that below directory
        $fileDir = '/../web/bookImages/';
        $fileNameDir = '/bookImages/';

        //GET Request Data
        $content = $request->get('data');
        $data = json_decode($content, true);

        $bookDealData = $data['bookDealData'];

        // Image Files
        $files = $request->files;

        $bookDealData['bookDealImages'] = array();

        //Upload All Deal Images
        $fileUploadError = false;

        $imageArray=array();
        for($i=1;$i<count($bookDealData['bookImages']);$i++){
            array_push($imageArray,array(
               'imageUrl'=> $bookDealData['bookImages'][$i]['image']
            ));
        }

        foreach ($files as $file) {
            if ((($file->getSize()) / 1024) <= 300) {

                $fileSaveName = gmdate("Y-d-m_h_i_s_") . rand(0, 99999999) . "." . 'jpg';
                $file->move($fileDirHost . $fileDir, $fileSaveName);
                $this->_resize(330,500,$fileDirHost.$fileDir.$fileSaveName,$fileDirHost.$fileDir.$fileSaveName);

                array_push($imageArray,array(
                   'imageUrl'=> $fileNameDir.$fileSaveName
                ));

            } else {
                $fileUploadError = true;
            }
        }


        //If Error Occurs than Return Error Message
        if($fileUploadError)return $this->_createJsonResponse('error', array(
            'errorTitle' => "Cannot Update Book Deal",
            'errorDescription' => "Some Files are more than 300 KB",
            'errorTitleKey' => "COULD_NOT_UPDATE_BOOK_DEAL",
            'errorDescriptionKey' => "SOME_FILES_ARE_MORE_THAN_300_KB"), 400);



        $bookDealRepo=$em->getRepository('AppBundle:BookDeal');
        $bookDeal = $bookDealRepo->findOneById($data['bookDealData']['bookDealId']);

        //Check If User owns that deal
        if($bookDeal->getSeller()->getId()==$this->container->get('security.token_storage')->getToken()->getUser()->getId() && $bookDeal->getBookSellingStatus()== "Selling"){

            $em->getConnection()->beginTransaction();

            //Remove Older Images
            foreach($bookDeal->getBookDealImages() as $image){
                $bookDeal->removeBookDealImage($image);
            }
            $em->persist($bookDeal);
            $em->flush();

            $bookDealForm = $this->createForm(new BookDealType(), $bookDeal);

            $bookDealForm->remove('seller');
            $bookDealForm->remove('bookSellingStatus');
            $bookDealForm->remove('bookStatus');
            $bookDealForm->remove('bookViewCount');
            $bookDealForm->remove('book');
            $bookDealForm->remove('bookSubmittedDateTime');
            $bookDealForm->remove('buyer');

            //Images
            $bookDealData['bookDealImages']=$imageArray;

            $date = new \DateTime($bookDealData['bookAvailableDate']);
            $bookDealData['bookAvailableDate'] = $date->format("Y-m-d");
            //Set Email on Book Deal
            if(!array_key_exists('bookContactEmail',$bookDealData)){
                $bookDealData['bookContactEmail'] = $this->container->get('security.token_storage')->getToken()->getUser()->getEmail();
            }

            $bookDealForm->submit($bookDealData);

            if ($bookDealForm->isValid()) {
                try {
                    $em->persist($bookDeal);
                    $em->flush();
                    $em->getConnection()->commit();
                    $logData = array(
                        'user' => $this->get('security.token_storage')->getToken()->getUser()->getId(),
                        'logType' => "Update Book Deal",
                        'logDateTime' => gmdate('Y-m-d H:i:s'),
                        'logDescription' => $this->get('security.token_storage')->getToken()->getUser()->getUsername() . " has updated a book deal",
                        'userIpAddress' => $this->container->get('request')->getClientIp(),
                        'logUserType' => in_array("ROLE_ADMIN_USER", $this->get('security.token_storage')->getToken()->getUser()->getRoles()) ? "Admin User" : "Normal User"
                    );
                    $this->_saveLog($logData);

                    return $this->_createJsonResponse('success', array(
                        "successTitle" => "Book Deal Updated",
                        "successDescription" => "This book deal has been updated into your selling book list",
                        "successTitleKey" => "BOOK_DEAL_UPDATED",
                        "successDescriptionKey" => "THIS_BOOK_DEAL_HAS_BEEN_UPDATED_INTO_YOUR_SELLING_BOOK"
                        ), 200);
                }catch (\Exception $e){
                    $em->getConnection()->rollBack();
                    return $this->_createJsonResponse('error', array(
                        "errorTitle"=>"Could not update Book Deal",
                        "errorTitleKey" => "COULD_NOT_UPDATE_BOOK_DEAL",
                        "errorData" => $bookDealForm), 400);
                }
            } else {
                return $this->_createJsonResponse('error', array(
                    "errorTitle"=>"Could not update Book Deal",
                    "errorDescription"=>"Please check the form and try again",
                    "errorTitleKey" => "COULD_NOT_UPDATE_BOOK_DEAL",
                    "errorDescriptionKey" => "PLEASE_CHECK_THE_FORM_TRY_AGAIN",
                    "errorData" => $bookDealForm), 400);

            }
        }else{
            return $this->_createJsonResponse('error', array(
                'errorTitle' => "Cannot Update Book Deal",
                'errorDescription' => "You are not owner of that book deal or the book is already sold",
                "errorTitleKey" => "COULD_NOT_UPDATE_BOOK_DEAL",
                "errorDescriptionKey" => "YOU_ARE_NOT_OWNER_OF_THAT_BOOK_DEAL_OR"), 400);
        }


    }

    /**
     * Delete Book Deal
     */
    function deleteBookDealAction(Request $request){
        $content = $request->getContent();
        $data = json_decode($content, true);

        $userId = $this->container->get('security.token_storage')->getToken()->getUser()->getId();
        $em = $this->getDoctrine()->getManager();
        $bookDealRepo=$em->getRepository('AppBundle:BookDeal');

        $bookDeal = $bookDealRepo->findOneById($data['bookDealId']);
        if($bookDeal->getSeller()->getId()==$userId){
            if($bookDeal->getBuyer()!==null){
                return $this->_createJsonResponse('error', array(
                    'errorTitle' => "Cannot Delete Book Deal",
                    'errorDescription' => "This book is sold.",
                    'errorTitleKey' => "CAN_NOT_DELETE_BOOK_DEAL",
                    'errorDescriptionKey' => "THIS_BOOK_IS_SOLD"
                ), 400);
            }else {
                $em->remove($bookDeal);
                $em->flush();

                $logData = array(
                    'user' => $this->get('security.token_storage')->getToken()->getUser()->getId(),
                    'logType' => "Delete Book Deal",
                    'logDateTime' => gmdate('Y-m-d H:i:s'),
                    'logDescription' => $this->get('security.token_storage')->getToken()->getUser()->getUsername() . " has deleted a book deal named " . $bookDeal->getBook()->getBookTitle(),
                    'userIpAddress' => $this->container->get('request')->getClientIp(),
                    'logUserType' => in_array("ROLE_ADMIN_USER", $this->get('security.token_storage')->getToken()->getUser()->getRoles()) ? "Admin User" : "Normal User"
                );
                $this->_saveLog($logData);

                return $this->_createJsonResponse('success', array(
                    'successTitle' => "Book Deal is Deleted",
                    'successTitleKey' => "BOOK_DEAL_IS_DELETED"), 200);
            }
        }else{
            return $this->_createJsonResponse('error', array(
                'errorTitle' => "Cannot Delete Book Deal",
                'errorDescription' => "You are not owner of that book deal.",
                'errorTitleKey' => "CAN_NOT_DELETE_BOOK_DEAL",
                'errorDescriptionKey' => "YOU_ARE_NOT_OWNER_OF_THAT_BOOK_DEAL"), 400);
        }
    }

    function getActivatedBookDealOfUserAction(Request $request){

        $content = $request->getContent();
        $data = json_decode($content, true);


        $em = $this->getDoctrine()->getManager();
        $bookDealRepo = $em->getRepository('AppBundle:BookDeal');
        $userRepo = $em->getRepository('AppBundle:User');

        $user = $userRepo->findOneBy(array('username'=>$data['username']));

        if($user instanceof User){

            $deals=array();

            $bookDeals = $bookDealRepo->getActivatedBooksUserHasCreated($user->getId(),$data['pageNumber'],$data['pageSize'],$data['keyword']);
            $bookDealsNumber = $bookDealRepo->getActivatedBooksUserHasCreatedTotalNumber($user->getId(),$data['keyword']);
            $userData=array(
                'username'=>$user->getUsername(),
                'university'=>$user->getCampus()->getUniversity()->getUniversityName()
            );
            //Set Subtitle in Book
            for ($i = 0; $i < count($bookDeals); $i++) {
                $bookDeals[$i]['contacts'] = array();
                if (strpos($bookDeals[$i]['bookTitle'], ":")) {
                    $bookDeals[$i]['bookSubTitle'] = substr($bookDeals[$i]['bookTitle'], strpos($bookDeals[$i]['bookTitle'], ":") + 2);
                    $bookDeals[$i]['bookTitle'] = substr($bookDeals[$i]['bookTitle'], 0, strpos($bookDeals[$i]['bookTitle'], ":"));
                }

            }

            //Getting Deals I have created
            foreach ($bookDeals as $deal) {

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

                array_push($deals,$deal);

            }

            return $this->_createJsonResponse('success', array(
                'successData' => array(
                    'userData'=>$userData,
                    'result'=>$deals,
                    'totalNumber'=>$bookDealsNumber
                )
            ), 200);


        }else{
            return $this->_createJsonResponse('error', array(
                'errorTitle' => "Sorry, No User Found in that Username"
            ), 400);
        }

    }

    /**
     * Get All Activated Selling and Contacted and Sold and Bought Book Deals of User
     */
    public function getAllActivatedDealsForMessageBoardAction(Request $request){

        $userEntity = $this->container->get('security.token_storage')->getToken()->getUser();

        if($userEntity instanceof User){
            $em = $this->getDoctrine()->getManager();
            $userRepo = $em->getRepository('AppBundle:User');
            $bookDealRepo = $em->getRepository('AppBundle:BookDeal');


            //Getting Selling Book Deals
            $sellingBookDeals = $bookDealRepo->getAllActivatedSellingBookOfUser($userEntity->getId());

            //Set Subtitle For Selling Book Deals
            for ($i = 0; $i < count($sellingBookDeals); $i++) {
                //Setting deal type selling or contacted
                $sellingBookDeals[$i]['dealType']="sellingDeal";

                //Setting Subtitle
                $sellingBookDeals[$i]['contacts'] = array();
                if (strpos($sellingBookDeals[$i]['bookTitle'], ":")) {
                    $sellingBookDeals[$i]['bookSubTitle'] = substr($sellingBookDeals[$i]['bookTitle'], strpos($sellingBookDeals[$i]['bookTitle'], ":") + 2);
                    $sellingBookDeals[$i]['bookTitle'] = substr($sellingBookDeals[$i]['bookTitle'], 0, strpos($sellingBookDeals[$i]['bookTitle'], ":"));
                }
            }
            //Getting Contacts For Selling Book Deals
            $contacts = $bookDealRepo->getContactsOfBookDeals($sellingBookDeals);

            //Adding Contacts according to deals
            if($contacts==null){
                $contacts=array();
            }
            foreach ($contacts as $contact) {
                for ($i = 0; $i < count($sellingBookDeals); $i++) {

                    if ((int)$contact['bookDealId'] == (int)$sellingBookDeals[$i]['bookDealId']) {

                        if ($contact['buyerNickName'] == null) {
                            $user = $userRepo->findById((int)$contact['buyerId']);
                            $contact['contactName'] = $user[0]->getUsername();
                        }
                        $contact['contactEmail'] = $contact['buyerEmail'];
                        $date = $contact['contactDateTime']->format('H:i d M Y');

                        $contact['contactDateTimeFormatted']=$date;

                        array_push($sellingBookDeals[$i]['contacts'], $contact);
                    }
                }
            }






            //Getting Contacted Book Deals
            $contactedBookDeals = $bookDealRepo->getAllActivatedContactedBookOfUser($userEntity->getId());

            //Set Subtitle in Book
            for ($i = 0; $i < count($contactedBookDeals); $i++) {
                //Setting deal type selling or contacted
                $contactedBookDeals[$i]['dealType']="contactedDeal";

                //Setting Subtitle
                $contactedBookDeals[$i]['contacts'] = array();
                if (strpos($contactedBookDeals[$i]['bookTitle'], ":")) {
                    $contactedBookDeals[$i]['bookSubTitle'] = substr($contactedBookDeals[$i]['bookTitle'], strpos($contactedBookDeals[$i]['bookTitle'], ":") + 2);
                    $contactedBookDeals[$i]['bookTitle'] = substr($contactedBookDeals[$i]['bookTitle'], 0, strpos($contactedBookDeals[$i]['bookTitle'], ":"));
                }

            }

            //Getting Contacts
            $contacts = $bookDealRepo->getContactsOfBookDeals($contactedBookDeals);
            //Adding Contacts according to deals
            if($contacts==null){
                $contacts=array();
            }
            foreach ($contacts as $contact) {

                for ($i = 0; $i < count($contactedBookDeals); $i++) {

                    if ((int)$contact['bookDealId'] == (int)$contactedBookDeals[$i]['bookDealId'] && $contact['buyerId']==$userEntity->getId()) {

                        $contact['profilePicture'] = $contactedBookDeals[$i]['sellerProfilePicture'];
                        $contact['contactName'] = $contactedBookDeals[$i]['sellerUsername'];
                        $contact['contactEmail'] = $contactedBookDeals[$i]['sellerEmail'];
                        $date = $contact['contactDateTime']->format('H:i d M Y');

                        $contact['contactDateTimeFormatted']=$date;

                        array_push($contactedBookDeals[$i]['contacts'], $contact);
                    }
                }

            }




            //Getting Sold Book Deals
            $soldBookDeals = $bookDealRepo->getAllActivatedSoldBookOfUser($userEntity->getId());

            //Set Subtitle For Selling Book Deals
            for ($i = 0; $i < count($soldBookDeals); $i++) {
                //Setting deal type selling or contacted
                $soldBookDeals[$i]['dealType']="soldDeal";

                //Setting Subtitle
                $soldBookDeals[$i]['contacts'] = array();
                if (strpos($soldBookDeals[$i]['bookTitle'], ":")) {
                    $soldBookDeals[$i]['bookSubTitle'] = substr($soldBookDeals[$i]['bookTitle'], strpos($soldBookDeals[$i]['bookTitle'], ":") + 2);
                    $soldBookDeals[$i]['bookTitle'] = substr($soldBookDeals[$i]['bookTitle'], 0, strpos($soldBookDeals[$i]['bookTitle'], ":"));
                }
            }
            //Getting Contacts For Selling Book Deals
            $contacts = $bookDealRepo->getBuyerContactsOfBookDeals($soldBookDeals);

            //Adding Contacts according to deals
            if($contacts==null){
                $contacts=array();
            }
            foreach ($contacts as $contact) {
                for ($i = 0; $i < count($soldBookDeals); $i++) {

                    if ((int)$contact['bookDealId'] == (int)$soldBookDeals[$i]['bookDealId']) {

                        if ($contact['buyerNickName'] == null) {
                            $user = $userRepo->findById((int)$contact['buyerId']);
                            $contact['contactName'] = $user[0]->getUsername();
                        }
                        $contact['contactEmail'] = $contact['buyerEmail'];
                        $date = $contact['contactDateTime']->format('H:i d M Y');

                        $contact['contactDateTimeFormatted']=$date;

                        array_push($soldBookDeals[$i]['contacts'], $contact);
                    }
                }
            }






            //Getting Bought Book Deals
            $boughtBookDeals = $bookDealRepo->getAllActivatedBoughtBookOfUser($userEntity->getId());

            //Set Subtitle in Book
            for ($i = 0; $i < count($boughtBookDeals); $i++) {
                //Setting deal type selling or contacted
                $boughtBookDeals[$i]['dealType']="boughtDeal";

                //Setting Subtitle
                $boughtBookDeals[$i]['contacts'] = array();
                if (strpos($boughtBookDeals[$i]['bookTitle'], ":")) {
                    $boughtBookDeals[$i]['bookSubTitle'] = substr($boughtBookDeals[$i]['bookTitle'], strpos($boughtBookDeals[$i]['bookTitle'], ":") + 2);
                    $boughtBookDeals[$i]['bookTitle'] = substr($boughtBookDeals[$i]['bookTitle'], 0, strpos($boughtBookDeals[$i]['bookTitle'], ":"));
                }

            }

            //Getting Contacts
            $contacts = $bookDealRepo->getContactsOfBookDeals($boughtBookDeals);
            //Adding Contacts according to deals
            if($contacts==null){
                $contacts=array();
            }
            foreach ($contacts as $contact) {

                for ($i = 0; $i < count($boughtBookDeals); $i++) {

                    if ((int)$contact['bookDealId'] == (int)$boughtBookDeals[$i]['bookDealId'] && $contact['buyerId']==$userEntity->getId()) {

                        $contact['profilePicture'] = $boughtBookDeals[$i]['sellerProfilePicture'];
                        $contact['contactName'] = $boughtBookDeals[$i]['sellerUsername'];
                        $contact['contactEmail'] = $boughtBookDeals[$i]['sellerEmail'];
                        $date = $contact['contactDateTime']->format('H:i d M Y');

                        $contact['contactDateTimeFormatted']=$date;

                        array_push($boughtBookDeals[$i]['contacts'], $contact);
                    }
                }

            }




            //Merging and sorting array
            $bookDeals =array_merge($sellingBookDeals,$contactedBookDeals,$soldBookDeals,$boughtBookDeals);
            $deals=array();

            for($i=0;$i<count($bookDeals);$i++){
                $val = str_pad($bookDeals[$i]['bookDealId'], 10, "0", STR_PAD_LEFT);
                $deals[$val]=$bookDeals[$i];
            }

            krsort($deals);
            $newArray=array();
            foreach($deals as $deal){
                array_push($newArray,$deal);
            }


            //Add Star into BookDeals

            $starRepo = $em->getRepository("AppBundle:Star");
            $starredBookDeals = $starRepo->findBy(array('user'=>$userEntity->getId()));
            for($i=0;$i<count($newArray);$i++){
                $newArray[$i]['starred']=false;
                foreach($starredBookDeals as $deal){
                    if($deal->getBookDeal()->getId()==$newArray[$i]['bookDealId']){
                        $newArray[$i]['starred']=true;
                    }
                }
            }

            $finalArray=array();
            foreach($newArray as $deal){
                if(count($deal['contacts'])>0){
                    array_push($finalArray,$deal);
                }
            }

            return $this->_createJsonResponse('success',array('successData'=>$finalArray),200);
        }else{
            return $this->_createJsonResponse('error',array('errorTitle'=>"Sorry No Data was found"),400);
        }

    }

    /**
     * Get All Activated Selling and Contacted Book Deals of User
     */
    public function getAllDataForNewContactInMessageBoardAction(Request $request){

        $userEntity = $this->container->get('security.token_storage')->getToken()->getUser();

        //GET Request Data
        $content = $request->getContent();
        $data = json_decode($content, true);

        $em = $this->getDoctrine()->getManager();
        $userRepo = $em->getRepository('AppBundle:User');
        $bookDealRepo = $em->getRepository('AppBundle:BookDeal');
        $contactData = $bookDealRepo->getAllDataForNewContactInMessageBoard($data['contactId'],$userEntity->getId());

        for ($i = 0; $i < count($contactData); $i++) {
            //Setting deal type selling or contacted
            $contactData[$i]['dealType']="sellingDeal";

            //Setting Subtitle
            $contactData[$i]['contacts'] = array();
            if (strpos($contactData[$i]['bookTitle'], ":")) {
                $contactData[$i]['bookSubTitle'] = substr($contactData[$i]['bookTitle'], strpos($contactData[$i]['bookTitle'], ":") + 2);
                $contactData[$i]['bookTitle'] = substr($contactData[$i]['bookTitle'], 0, strpos($contactData[$i]['bookTitle'], ":"));
            }
        }

        //Getting Contacts For Selling Book Deals
        $contacts = $bookDealRepo->getContactsOfBookDeals($contactData);

        //Adding Contacts according to deals
        if($contacts==null){
            $contacts=array();
        }
        foreach ($contacts as $contact) {
            for ($i = 0; $i < count($contactData); $i++) {

                if ((int)$contact['bookDealId'] == (int)$contactData[$i]['bookDealId']) {

                    if ($contact['buyerNickName'] == null) {
                        $user = $userRepo->findById((int)$contact['buyerId']);
                        $contact['contactName'] = $user[0]->getUsername();
                    }
                    $contact['contactEmail'] = $contact['buyerEmail'];
                    $date = $contact['contactDateTime']->format('H:i d M Y');

                    $contact['contactDateTimeFormatted']=$date;

                    array_push($contactData[$i]['contacts'], $contact);
                }
            }
        }

        return $this->_createJsonResponse('success',array('successData'=>$contactData),200);

    }


    // New Image  Resize function
    public function _resize($newWidth , $newHeight, $targetFile, $originalFile) {

        $info = getimagesize($originalFile);
        $mime = $info['mime'];

        switch ($mime) {
            case 'image/jpeg':
                $image_create_func = 'imagecreatefromjpeg';
                $image_save_func = 'imagejpeg';
//                $new_image_ext = 'jpg';
                break;

            case 'image/png':
                $image_create_func = 'imagecreatefrompng';
                $image_save_func = 'imagepng';
//                $new_image_ext = 'png';
                break;

            case 'image/gif':
                $image_create_func = 'imagecreatefromgif';
                $image_save_func = 'imagegif';
//                $new_image_ext = 'gif';
                break;

            default:
                throw new Exception('Unknown image type.');
        }

        $img = $image_create_func($originalFile);
        list($width, $height) = getimagesize($originalFile);

//        $newHeight = ($height / $width) * $newWidth;
        $tmp = imagecreatetruecolor($newWidth, $newHeight);
        imagecopyresampled($tmp, $img, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        if (file_exists($targetFile)) {
            unlink($targetFile);
        }
        $image_save_func($tmp, "$targetFile");
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
