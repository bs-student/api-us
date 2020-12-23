<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\Book;
use AppBundle\Entity\BookDeal;
use AppBundle\Entity\Campus;
use AppBundle\Entity\Log;
use AppBundle\Form\Type\BookDealType;
use AppBundle\Form\Type\LogType;
use AppBundle\Form\Type\UniversityType;
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

class BookManagementApiController extends Controller
{


    /**
     * Search By Keyword Amazon Api (Normal Call)
     */
    public function searchByKeywordAmazonApiAction(Request $request)
    {
        if ($this->_headerTokenDecode($request->headers->all())) {

            $content = $request->getContent();
            $data = json_decode($content, true);

            if (array_key_exists('keyword', $data)) {
                $keyword = $data['keyword'];
            } else {
                $keyword = null;
            }
            if (array_key_exists('page', $data)) {
                $page = $data['page'];
            } else {
                $page = null;
            }
            return $this->_getBooksByKeywordAmazon($keyword, $page);
        }else{
            return $this->_createJsonResponse('error', array('errorTitle' => "Unauthorized Access Denied"), 400);
        }
    }

    /**
     * Search By Keyword Amazon Api (Api Call)
     */
    public function searchByKeywordAmazonApiWithApiCallAction(Request $request)
    {

        $content = $request->getContent();
        $data = json_decode($content, true);

        if (array_key_exists('keyword', $data)) {
            $keyword = $data['keyword'];
        } else {
            $keyword = null;
        }
        if (array_key_exists('page', $data)) {
            $page = $data['page'];
        } else {
            $page = null;
        }
        return $this->_getBooksByKeywordAmazon($keyword, $page);

    }

    /**
     * Get Lowest Online Price Campus Books Api
     */
    public function getLowestPriceByIsbnCampusBooksApiAction(Request $request)
    {
        if ($this->_headerTokenDecode($request->headers->all())) {
            $isbn = $request->query->get('isbn');
            if ($isbn != null) {
                $lowestOnlinePrice = $this->_getBooksLowestPriceByIsbnCampusBooks($isbn);
                if ($lowestOnlinePrice) {
                    return $this->_createJsonResponse('success', array('successData' => array('bookPriceOnlineLowest' => $lowestOnlinePrice)), 200);
                } else {
                    return $this->_createJsonResponse('error', array('errorTitle' => "No Price Found"), 400);
                }

            } else {
                return $this->_createJsonResponse('error', array('errorTitle' => "Invalid Isbn"), 400);
            }
        }else{
            return $this->_createJsonResponse('error', array('errorTitle' => "No Price Found"), 400);
        }

    }


    /**
     * Search By ASIN Amazon API
     */
    public function searchByAsinAmazonApiAction(Request $request)
    {
        if ($this->_headerTokenDecode($request->headers->all())) {
            $content = $request->getContent();
            $data = json_decode($content, true);

            if (array_key_exists('asin', $data) && is_numeric($data['asin']) && (strlen($data['asin']) == 10 || strlen($data['asin']) == 13)) {
                $asin = $data['asin'];
                return $this->_getBooksByAsinAmazon($asin);
            } else {
                return $this->_createJsonResponse('error', array('errorTitle' => "No Book was found", "errorDescription" => "Please provide real ISBN Number"), 400);
            }

//            return $this->_getBooksByAsinAmazon($asin);
        }else{
            return $this->_createJsonResponse('error', array('errorTitle' => "Unauthorized Access Denied"), 400);
        }
    }

    /**
     * Search Book By ISBN Amazon API
     */
    public function searchByIsbnAmazonApiAction(Request $request)
    {

        $content = $request->getContent();
        $data = json_decode($content, true);

        if (array_key_exists('isbn', $data)) {
            $isbn = $data['isbn'];
        } else {
            $isbn = "";
        }

        return $this->_getBooksByIsbnAmazon($isbn);

    }


    /**
     * Search By ISBN Campus Books APi
     */
    public function searchByIsbnCampusBooksApiAction(Request $request)
    {
        if ($this->_headerTokenDecode($request->headers->all())) {
            $content = $request->getContent();
            $data = json_decode($content, true);

            if (array_key_exists('isbn', $data)) {
                $isbn = $data['isbn'];
            } else {
                $isbn = "";
            }

            return $this->_getBooksByIsbnCampusBooks($isbn);
        }else{
            return $this->_createJsonResponse('error', array('errorTitle' => "Unauthorized Access Denied"), 400);
        }
    }

    /**
     * Get Amazon Cart Create Url
     */
    public function getAmazonCartCreateUrlAction(Request $request)
    {

        $content = $request->getContent();
        $data = json_decode($content, true);

        if (array_key_exists('bookOfferId', $data)) {
            $bookOfferId = $data['bookOfferId'];
        } else {
            $bookOfferId = "";
        }

        $addToCartAmazonUrl = $this->_addToCartAmazonUrl($bookOfferId);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $addToCartAmazonUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $xmlOutput  = curl_exec($ch);
        curl_close($ch);


        $fileContents = str_replace(array("\n", "\r", "\t"), '', $xmlOutput);

        $fileContents = trim(str_replace('"', "'", $fileContents));

        $simpleXml = simplexml_load_string($fileContents);
        $cartUrl = (string)$simpleXml->Cart->PurchaseURL;
        $amazonApiInfo = $this->_getAmazonSearchParams();

        if (empty($cartUrl))
        {
            $cartUrl = "https://www.amazon.com/exec/obidos/tg/detail/-/" . $bookOfferId ."/". $amazonApiInfo['params']['AssociateTag'] ;

        }

        return $this->_createJsonResponse('success', array('successData' => array('cartUrl' => $cartUrl)), 200);


    }


    /**
     * Get Campus Deals by ISBN Api Call with AccessToken
     */
    public function getCampusDealsByIsbnApiAction(Request $request)
    {
        $content = $request->getContent();
        $data = json_decode($content, true);

        $campusId = $this->get('security.token_storage')->getToken()->getUser()->getCampus()->getId();

        $em = $this->getDoctrine()->getManager();
        $bookDealRepo = $em->getRepository('AppBundle:BookDeal');

        if ($data != null) {
            if (array_key_exists('isbn', $data)) {
                $deals = array(
                    'buyerToSeller' => array(),
                    'sellerToBuyer' => array(),
                    'student2studentBoard' => array(),
                );
                $onCampusDeals = $bookDealRepo->getCampusDealsByIsbn($data['isbn'], $campusId);

                //Increase View Counter
                if(count($onCampusDeals)>0){
                    $bookDealRepo->increaseBookViewCounter($onCampusDeals);

                    foreach ($onCampusDeals as $deal) {

                        //Formatting Date
                        if ($deal['bookAvailableDate'] != null) {
                            $deal['bookAvailableDate'] = $deal['bookAvailableDate']->format('d M Y');
                        }

                        //dividing via Contact Method
                        if (strpos('buyerToSeller', $deal['bookContactMethod']) !== false) {
                            array_push($deals['buyerToSeller'], $deal);
                        } else if(strpos('sellerToBuyer', $deal['bookContactMethod'])!== false) {
                            array_push($deals['sellerToBuyer'], $deal);
                        } else if(strpos('student2studentBoard', $deal['bookContactMethod'])!== false){
                            array_push($deals['student2studentBoard'], $deal);
                        }

                    }

                }


                return $this->_createJsonResponse('success', array('successData' => $deals), 200);

            } else {
                return $this->_createJsonResponse('error', array('errorTitle' => "Wrong Data Provided", 'errorDescription' => "Please Provide Isbn"), 400);
            }
        } else {
            return $this->_createJsonResponse('error', array('errorTitle' => "Wrong Data Provided", 'errorDescription' => "Please Provide Isbn"), 400);
        }

    }


    /**
     * Get Campus Deals by ISBN
     */
    public function getCampusDealsByIsbnAction(Request $request)
    {
        if ($this->_headerTokenDecode($request->headers->all())) {
            $content = $request->getContent();
            $data = json_decode($content, true);

            $em = $this->getDoctrine()->getManager();
            $bookDealRepo = $em->getRepository('AppBundle:BookDeal');

            if ($data != null) {
                if (array_key_exists('isbn', $data) && array_key_exists('campusId', $data)) {
                    $deals = array(
                        'buyerToSeller' => array(),
                        'sellerToBuyer' => array(),
                        'student2studentBoard' => array(),
                    );
                    $onCampusDeals = $bookDealRepo->getPublicCampusDealsByIsbn($data['isbn'], $data['campusId']);

                    //Increase View Counter
                    if (count($onCampusDeals) > 0) {
                        $bookDealRepo->increaseBookViewCounter($onCampusDeals);

                        foreach ($onCampusDeals as $deal) {

                            //Formatting Date
                            if ($deal['bookAvailableDate'] != null) {
                                $deal['bookAvailableDate'] = $deal['bookAvailableDate']->format('d M Y');
                            }

                            //dividing via Contact Method
                            if (strpos('buyerToSeller', $deal['bookContactMethod']) !== false) {
                                array_push($deals['buyerToSeller'], $deal);
                            } else if (strpos('sellerToBuyer', $deal['bookContactMethod']) !== false) {
                                array_push($deals['sellerToBuyer'], $deal);
                            } else if (strpos('student2studentBoard', $deal['bookContactMethod']) !== false) {
                                array_push($deals['student2studentBoard'], $deal);
                            }

                        }
                    }


                    return $this->_createJsonResponse('success', array('successData' => $deals), 200);

                } else {
                    return $this->_createJsonResponse('error', array('errorTitle' => "Wrong Data Provided", 'errorDescription' => "Please Provide Isbn"), 400);
                }
            } else {
                return $this->_createJsonResponse('error', array('errorTitle' => "Wrong Data Provided", 'errorDescription' => "Please Provide Isbn"), 400);
            }
        }else{
            return $this->_createJsonResponse('error', array('errorTitle' => "Unauthorized Access Denied"), 400);
        }


    }


    /**
     * Sell New Book
     */
    public function addNewSellBookAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();

        //Get Image Save Dir
        $fileDirHost = $this->container->getParameter('kernel.root_dir');

        $fileDir = '/../web/bookImages/';
        $fileNameDir = '/bookImages/';

        //GET Request Data
        $content = $request->get('data');
        $data = json_decode($content, true);
        $bookData = $data['bookData'];
        $bookDealData = $data['bookDealData'];

        // Image Files
        $files = $request->files;
        $bookDealData['bookDealImages'] = array();



        //Upload All Deal Images
        $fileUploadError = false;
        foreach ($files as $file) {
            if ((($file->getSize()) / 1024) <= 300) {
                $fileSaveName = gmdate("Y-d-m_h_i_s_") . rand(0, 99999999) . "." . pathinfo($file->getClientOriginalName())['extension'];
                $file->move($fileDirHost . $fileDir, $fileSaveName);
                $bookImageArray = array();
                $bookImageArray['imageUrl'] = $fileNameDir . $fileSaveName;
                array_push($bookDealData['bookDealImages'], $bookImageArray);
            } else {
                $fileUploadError = true;
            }
        }
        //If Error Occurs than Return Error Message
        if($fileUploadError)return $this->_createJsonResponse('error', array(
            'errorTitle' => "Cannot Add Sell Book",
            'errorDescription' => "Some Files are more than 300 KB",
            "errorTitleKey" => "COULD_NOT_ADD_BOOK_ON_SELL_PAGE",
            "errorDescriptionKey" => "SOME_FILES_ARE_MORE_THAN_300_KB"
            ), 400);



        if(array_key_exists('bookType',$bookData)){

            /* Inserting Book Info */

            //Check if Book Already Exists in DB

            $alreadyExistedBook = $this->_checkIfBookExistInDatabase($bookData['bookIsbn10']);


            $bookId = null;

            if (!($alreadyExistedBook instanceof Book)) {
                // Check if Its a custom book or got from amazon
                if(!strcmp('newSellBook',$bookData['bookType'])){

                    //Insert Book Image from amazon
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $bookData['bookImage']);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                    $imageOutput  = curl_exec($ch);
                    curl_close($ch);

                    $fileSaveName = gmdate("Y-d-m_h_i_s_") . rand(0, 99999999) . ".jpg";
                    $fp = fopen($fileDirHost . $fileDir . $fileSaveName, 'x');
                    fwrite($fp, $imageOutput);
                    fclose($fp);
                    $bookData['bookImage'] = $fileNameDir . $fileSaveName;


                }elseif(!strcmp('newSellCustomBook',$bookData['bookType'])){

                    //Insert First Image from files
                    $initialBookImage = array_shift($bookDealData['bookDealImages']);
                    $bookData['bookImage'] = $initialBookImage['imageUrl'];
//                    $bookData['bookImage'] = $bookDealData['bookDealImages'][0]['imageUrl'];

                }


                //Insert New Book
                $book = new Book();
                $bookForm = $this->createForm(new BookType(), $book);

                $bookData['bookPublishDate'] = (new \DateTime($bookData['bookPublishDate']))->format("Y-m-d");

                if(array_key_exists('bookDescription',$bookData)){
                    $bookData['bookDescription'] = strip_tags($bookData['bookDescription']);
                }

                if(array_key_exists('bookSubTitle',$bookData)){
                    $bookData['bookTitle']=$bookData['bookTitle'].": ".$bookData['bookSubTitle'];
                }

                $bookForm->submit($bookData);

                if ($bookForm->isValid()) {
                    $em->persist($book);
                    $em->flush();
                    $bookId = $book->getId();

                    $logData = array(
                        'user'=>$this->get('security.token_storage')->getToken()->getUser()->getId(),
                        'logType'=>"Add Book",
                        'logDateTime'=>gmdate('Y-m-d H:i:s'),
                        'logDescription'=> $this->get('security.token_storage')->getToken()->getUser()->getUsername()." has added a book named ".$book->getBookTitle(),
                        'userIpAddress'=>$this->container->get('request')->getClientIp(),
                        'logUserType'=> in_array("ROLE_ADMIN_USER",$this->get('security.token_storage')->getToken()->getUser()->getRoles())?"Admin User":"Normal User"
                    );
                    $this->_saveLog($logData);

                } else {
                    return $this->_createJsonResponse('error', array("errorData" => $bookForm), 400);
                }

            } else {
                $bookId = $alreadyExistedBook->getId();
            }

            /* Inserting Book Deal */


            $bookDeal = new BookDeal();
            $bookDealForm = $this->createForm(new BookDealType(), $bookDeal);

            $date = new \DateTime($bookDealData['bookAvailableDate']);
            $bookDealData['bookAvailableDate'] = $date->format("Y-m-d");
            $bookDealData['seller'] = $this->container->get('security.token_storage')->getToken()->getUser()->getId();
            $bookDealData['bookSellingStatus'] = "Selling";
            $bookDealData['bookStatus'] = "Activated";
            $bookDealData['bookViewCount'] = 0;
            $bookDealData['book'] = $bookId;
            $bookDealData['bookSubmittedDateTime'] =  gmdate('Y-m-d H:i:s');
            //Set Email on Book Deal
            if(!array_key_exists('bookContactEmail',$bookDealData)){
//                $bookDealData['bookContactEmail'] = $this->container->get('security.token_storage')->getToken()->getUser()->getEmail();
                $bookDealData['bookContactEmail'] = $this->container->get('security.token_storage')->getToken()->getUser()->getStandardEmail()
                    ?$this->container->get('security.token_storage')->getToken()->getUser()->getStandardEmail()
                    :$this->container->get('security.token_storage')->getToken()->getUser()->getEmail();
            }


            $bookDealForm->submit($bookDealData);

            if ($bookDealForm->isValid()) {
                $em->persist($bookDeal);
                $em->flush();

                $logData = array(
                    'user'=>$this->get('security.token_storage')->getToken()->getUser()->getId(),
                    'logType'=>"Add Book Deal",
                    'logDateTime'=>gmdate('Y-m-d H:i:s'),
                    'logDescription'=> $this->get('security.token_storage')->getToken()->getUser()->getUsername()." has added a book deal priced $".$bookDealData['bookPriceSell'],
                    'userIpAddress'=>$this->container->get('request')->getClientIp(),
                    'logUserType'=> in_array("ROLE_ADMIN_USER",$this->get('security.token_storage')->getToken()->getUser()->getRoles())?"Admin User":"Normal User"
                );
                $this->_saveLog($logData);

                return $this->_createJsonResponse('success', array(
                    "successTitle" => "Book has been successfully posted",
                    "successTitleKey" => "BOOK_HAS_BEEN_SUCCESSFULLY_POSTED"), 200);
            } else {
                return $this->_createJsonResponse('error', array("errorData" => $bookDealForm), 400);

            }


        }else{
            return $this->_createJsonResponse('error', array(
                "errorTitle" => "Could not add book on sell page",
                "errorDescription" => "Check the form and submit again",
                "errorTitleKey" => "COULD_NOT_ADD_BOOK_ON_SELL_PAGE",
                "errorDescriptionKey" => "CHECK_THE_FORM_AND_SUBMIT_AGAIN"
                ), 400);
        }

    }


    /**
     * Sell New Custom Book
     */
    public function _addNewCustomSellBookAction(Request $request){

    }


    function _checkIfBookExistInDatabase($isbn10)
    {
        $em = $this->getDoctrine()->getManager();
        $bookRepo = $em->getRepository('AppBundle:Book');
        $book = $bookRepo->findOneBy(array('bookIsbn10' => $isbn10));
        if ($book) {
            return $book;
        } else {
            return false;
        }
    }

    function _getBooksByKeywordAmazon($keyword, $page)
    {

        $amazonCredentials = $this->_getAmazonSearchParams();

        $amazonCredentials['params']['Operation'] = "ItemSearch";
        $amazonCredentials['params']["ItemPage"] = $page;
        $amazonCredentials['params']["Keywords"] = $keyword;
        $amazonCredentials['params']["SearchIndex"] = "Books";
        $amazonCredentials['params']["ResponseGroup"] = "Medium,Offers";
        $getUrl = $this->_getUrlWithSignature($amazonCredentials);


        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $getUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $xmlOutput  = curl_exec($ch);
        curl_close($ch);

        $booksArray = $this->_parseMultipleBooksAmazonXmlResponse($xmlOutput);

        if(count($booksArray['books'])==0) {
            //Search through google book api
            $booksArray = $this->_getGoogleBooks($keyword, $page,10);
            //Search for manually entered book
            if (count($booksArray['books']) == 0) {
                $em = $this->getDoctrine()->getManager();
                $maxResultLimit = 10;
                $bookRepo = $em->getRepository("AppBundle:Book");
                $totalSearchNumber = $bookRepo->getCustomBookSearchNumber($keyword);
                $customBooks = $bookRepo->findCustomBook($keyword, $page, $maxResultLimit);
                $booksArray['totalSearchResults'] = $totalSearchNumber;
                if (count($customBooks) > 0) {
                    foreach ($customBooks as $book) {
                        $bookData = array(
                            'bookAsin' => $book['bookIsbn10'],
                            'bookTitle' => $book['bookTitle'],
                            'bookDirectorAuthorArtist' => $book['bookDirectorAuthorArtist'],
                            'bookPriceAmazon' => "Not Found",
                            'bookIsbn' => $book['bookIsbn10'],
                            'bookEan' => array_key_exists('bookIsbn13', $book) ? $book['bookIsbn13'] : "",
                            'bookEdition' => array_key_exists('bookEdition', $book) ? $book['bookEdition'] : "",
                            'bookPublisher' => array_key_exists('bookPublisher', $book) ? $book['bookPublisher'] : "",
                            'bookPublishDate' => $book['bookPublishDate']->format('d-M-Y'),
                            'bookBinding' => array_key_exists('bookBinding', $book) ? $book['bookBinding'] : "",
                            'bookDescription' => array_key_exists('bookDescription', $book) ? $book['bookDescription'] : "",
                            'bookPages' => array_key_exists('bookPages', $book) ? $book['bookPages'] : "",
                            'bookImages' => array(
                                array(
                                    'image' => $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['BASE'] . $book['bookImage'],
                                    'imageId' => 0
                                )
                            )
                        );
                        array_push($booksArray['books'], $bookData);
                    }
                }
            }
        }

        if (count($booksArray['books']) > 0) {
            $em = $this->getDoctrine()->getManager();
            $bookDealRepo = $em->getRepository("AppBundle:BookDeal");
            $user = $this->container->get('security.context')->getToken()->getUser();
            $campusId = null;
            if ($user != "anon.") {
                $campusId = $user->getCampus()->getId();
            }
            $studentBooks = $bookDealRepo->getStudentBooksWithMultipleISBN($booksArray['books'], $campusId);

            for ($i = 0; $i < count($booksArray['books']); $i++) {
                //Set Subtitle in Book
                /*If Logged In then Find Lowest Campus Price of Just users university
                    Otherwise if not logged in then find entire database and find the lowest price in whole student2student System
                */
                if (strpos($booksArray['books'][$i]['bookTitle'], ":")) {
                    $booksArray['books'][$i]['bookSubTitle'] = substr($booksArray['books'][$i]['bookTitle'], strpos($booksArray['books'][$i]['bookTitle'], ":") + 2);
                    $booksArray['books'][$i]['bookTitle'] = substr($booksArray['books'][$i]['bookTitle'], 0, strpos($booksArray['books'][$i]['bookTitle'], ":"));
                }

                // Getting Campus Lowest Price
                foreach ($studentBooks as $studentBook) {
                    if (!strcmp(strval($studentBook['bookIsbn10']), strval($booksArray['books'][$i]['bookIsbn']))) {
                        $booksArray['books'][$i]['bookPriceStudentLowest'] = "$" . number_format(floatval($studentBook['bookPriceSell']),2);
                        $booksArray['books'][$i]['bookPriceStudentLowestFound'] = true;
                        break;
                    }
                }

                foreach ($booksArray['books'] as $book) {
                    if (!array_key_exists('bookPriceStudentLowestFound', $book)) {
                        $booksArray['books'][array_search($book, $booksArray['books'])]['bookPriceStudentLowestFound'] = false;
                    }
                }
            }


            return $this->_createJsonResponse('success', array('successData' => $booksArray), 200);
        } else {
            return $this->_createJsonResponse('error', array('errorTitle' => "No Books were found", "errorDescription" => "Please Refine your search query and try again."), 400);
        }


    }
    function _getGoogleBooks($keyword, $page, $maxResult){
        $googleBookApiInfo = $this->getParameter('google_books_api_info');
        $searchData = (is_numeric($keyword) && (strlen($keyword) == 10 || strlen($keyword) == 13))? 'isbn:'.$keyword : preg_replace('/\s+/','',$keyword);
        $getUrl = $googleBookApiInfo['host'] . $searchData . '&key=' .$googleBookApiInfo['api_key'] .'&maxResults='. $maxResult . $googleBookApiInfo['uri'].(($page - 1) * 10);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $getUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $xmlOutput  = curl_exec($ch);
        curl_close($ch);
        $booksArray = array();
        $responseBooks = json_decode($xmlOutput,true);
        $totalSearchResults = isset($responseBooks['totalItems'])?$responseBooks['totalItems']:0;
        foreach ($responseBooks['items'] as $book){
            $volumeInfo = $book['volumeInfo'];
            $ISBN_10_key = array_search('ISBN_10', array_column($volumeInfo['industryIdentifiers'], 'type'));
            $ISBN_13_key = array_search('ISBN_13', array_column($volumeInfo['industryIdentifiers'], 'type'));

            $bookData = array(
                'bookAsin'=>array_key_exists('identifier',$volumeInfo['industryIdentifiers'][$ISBN_10_key])?$volumeInfo['industryIdentifiers'][$ISBN_10_key]['identifier']:"",
                'bookTitle'=>array_key_exists('title',$volumeInfo)?$volumeInfo['title']:"",
                'bookDirectorAuthorArtist'=>array_key_exists('authors',$volumeInfo)?$volumeInfo['authors'][0]:"",
                'bookPriceAmazon'=>"",
                'bookIsbn'=>array_key_exists('identifier',$volumeInfo['industryIdentifiers'][$ISBN_10_key])?$volumeInfo['industryIdentifiers'][$ISBN_10_key]['identifier']:"",
                'bookEan'=>array_key_exists('identifier',$volumeInfo['industryIdentifiers'][$ISBN_13_key])?$volumeInfo['industryIdentifiers'][$ISBN_13_key]['identifier']:"",
                'bookEdition'=>array_key_exists('edition',$volumeInfo)?$volumeInfo['edition']:"",
                'bookPublisher'=>array_key_exists('publisher',$volumeInfo)?$volumeInfo['publisher']:"",
                'bookPublishDate'=>array_key_exists('publishedDate',$volumeInfo)?date('d-M-Y',strtotime($volumeInfo['publishedDate'])):"",
                'bookBinding'=>array_key_exists('bookBinding',$volumeInfo)?$volumeInfo['bookBinding']:"Not Found",
                'bookDescription'=>array_key_exists('description',$volumeInfo)?$volumeInfo['description']:"",
                'bookPages'=>array_key_exists('pageCount',$volumeInfo)?$volumeInfo['pageCount']:"",
                'bookImages'=>array(
                    array(
                        'image'=>array_key_exists('imageLinks',$volumeInfo)?str_replace(array("http","zoom=1","edge=curl"),array("https","zoom=3", "edge=full"),$volumeInfo['imageLinks']['thumbnail']):"",
                        'imageId'=>0
                    )
                ),
                'bookOfferId'=>array_key_exists('identifier',$volumeInfo['industryIdentifiers'][$ISBN_10_key])?$volumeInfo['industryIdentifiers'][$ISBN_10_key]['identifier']:"",
            );
            array_push($booksArray,$bookData);

        }

        return array(
            'books' => $booksArray,
            'totalSearchResults' => $totalSearchResults
        );


    }

    function _getBooksByAsinAmazon($asin)
    {
        $em = $this->getDoctrine()->getManager();
        $bookRepo = $em->getRepository("AppBundle:Book");
        $amazonCredentials = $this->_getAmazonSearchParams();

        $amazonCredentials['params']['Operation'] = "ItemLookup";
        $amazonCredentials['params']["ItemId"] = $asin;
        $amazonCredentials['params']["ResponseGroup"] = "Medium,Offers";
        $getUrl = $this->_getUrlWithSignature($amazonCredentials);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $getUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $xmlOutput  = curl_exec($ch);
        curl_close($ch);
        $booksArray = $this->_parseMultipleBooksAmazonXmlResponse($xmlOutput);
        //search through google book api
        if (count($booksArray['books']) == 0){
            $page = 1;
            $maxResult = 1;
            $booksArray = $this->_getGoogleBooks($asin,$page,$maxResult);

            //Search for manually entered book
            if(count($booksArray['books'])==0){
                $em = $this->getDoctrine()->getManager();
                $bookRepo = $em->getRepository("AppBundle:Book");
                $customBooks = $bookRepo->findCustomBookByIsbn($asin);

                if(count($customBooks)>0){
                    foreach($customBooks as $book){
                        $bookData = array(
                            'bookAsin'=>$book['bookIsbn10'],
                            'bookTitle'=>$book['bookTitle'],
                            'bookDirectorAuthorArtist'=>$book['bookDirectorAuthorArtist'],
                            'bookPriceAmazon'=>"Not Found",
                            'bookIsbn'=>$book['bookIsbn10'],
                            'bookEan'=>array_key_exists('bookIsbn13',$book)?$book['bookIsbn13']:"",
                            'bookEdition'=>array_key_exists('bookEdition',$book)?$book['bookEdition']:"",
                            'bookPublisher'=>array_key_exists('bookPublisher',$book)?$book['bookPublisher']:"",
                            'bookPublishDate'=>$book['bookPublishDate']->format('d-M-Y'),
                            'bookBinding'=>array_key_exists('bookBinding',$book)?$book['bookBinding']:"",
                            'bookDescription'=>array_key_exists('bookDescription',$book)?$book['bookDescription']:"",
                            'bookPages'=>array_key_exists('bookPages',$book)?$book['bookPages']:"",
                            'bookImages'=>array(
                                array(
                                    'image'=>$_SERVER['REQUEST_SCHEME']."://".$_SERVER['HTTP_HOST'].$_SERVER['BASE'].$book['bookImage'],
                                    'imageId'=>0
                                )
                            )
                        );
                        array_push($booksArray['books'],$bookData);
                    }
                }
            }
        }


        if(count($booksArray['books'])>0){
            //Insert Book INTo DB
            $insertedBookId = $this->_insertBookIntoDatabase($booksArray['books'][0]);

            $images = array();
            if($insertedBookId){
                $bookImages = $bookRepo->getBookAndDealImages($insertedBookId);
                $insertedBook=$bookRepo->findOneById($insertedBookId);
                //GET FIRST IMAGE OF THAT BOOK
                array_push($images,array(
                    'image'=>$insertedBook->getBookImage(),
                    'imageId'=>0
                ));
                //GET All IMAGES OF THAT BOOK's DEALS
                for($i=0;$i<count($bookImages);$i++){
                    array_push($images,array(
                        'image'=>$bookImages[$i]['imageUrl'],
                        'imageId'=>($i+1)
                    ));
                }
                $booksArray['books'][0]['bookImages'] = $images;
                $booksArray['books'][0]['bookId'] = $insertedBookId;
            }



            $booksArray['books'][0]['bookDescription'] = strip_tags($booksArray['books'][0]['bookDescription']);

            //DONE 1.Insert Book into DB
            //DONE 2.GET All Images Of That Book With Deals & Add with Response
            //DONE 3.Return the DB response. Not the Amazon response
            //DONE 4.Increase View Number of Each Deal related to that Book (Do it on Second call)

            for ($i = 0; $i < count($booksArray['books']); $i++) {
                //Fixing Title & Sub Title
                if (strpos($booksArray['books'][$i]['bookTitle'], ":")) {
                    $booksArray['books'][$i]['bookSubTitle'] = substr($booksArray['books'][$i]['bookTitle'], strpos($booksArray['books'][$i]['bookTitle'], ":") + 2);
                    $booksArray['books'][$i]['bookTitle'] = substr($booksArray['books'][$i]['bookTitle'], 0, strpos($booksArray['books'][$i]['bookTitle'], ":"));
                }
                //Fixing Date
                if ($booksArray['books'][$i]['bookPublishDate'] != null) {
                    $booksArray['books'][$i]['bookPublishDate'] = (new \DateTime($booksArray['books'][$i]['bookPublishDate']))->format('d M Y');
                }

            }

        }


        if (count($booksArray['books']) > 0) {
            return $this->_createJsonResponse('success', array('successData' => $booksArray), 200);
        } else {
            return $this->_createJsonResponse('error', array('errorTitle' => "No Book was found", "errorDescription" => "Please provide real ASIN Number"), 400);
        }


    }

    function _insertBookIntoDatabase($book){

        $em = $this->getDoctrine()->getManager();
        $alreadyExistedBook = $this->_checkIfBookExistInDatabase($book['bookIsbn']);

        // Check if Book is already in DB
        if (!($alreadyExistedBook instanceof Book)) {

            //Insert Book Image from amazon
            $fileDirHost = $this->container->getParameter('kernel.root_dir');
            $fileDir = '/../web/bookImages/';
            $fileNameDir = '/bookImages/';

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $book['bookImages'][0]['image']);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            $imageOutput   = curl_exec($ch);
            curl_close($ch);

            $fileSaveName = gmdate("Y-d-m_h_i_s_") . rand(0, 99999999) . ".jpg";
            $fp = fopen($fileDirHost . $fileDir . $fileSaveName, 'x');
            fwrite($fp, $imageOutput);
            fclose($fp);

            $book['bookImage'] = $fileNameDir . $fileSaveName;

            //Insert New Book
            $bookEntity = new Book();
            $bookForm = $this->createForm(new BookType(), $bookEntity);

            $book['bookIsbn10']=$book['bookIsbn'];
            $book['bookIsbn13']=$book['bookEan'];
            $book['bookPublishDate'] = (new \DateTime($book['bookPublishDate']))->format("Y-m-d");
            $book['bookPage'] = $book['bookPages'];
            $book['bookDescription'] = strip_tags($book['bookDescription']);
            $book['bookAmazonPrice'] = number_format(floatval(substr($book['bookPriceAmazon'],1)),2);
            $bookForm->submit($book);

            if ($bookForm->isValid()) {
                $em->persist($bookEntity);
                $em->flush();
                return $bookEntity->getId();
            } else {
                return false;
            }

        }else{
            return $alreadyExistedBook->getId();
        }
    }

    function _getBooksByIsbnAmazon($isbn)
    {


        $amazonCredentials = $this->_getAmazonSearchParams();

        $amazonCredentials['params']['Operation'] = "ItemLookup";
        $amazonCredentials['params']["ItemId"] = $isbn;
        $amazonCredentials['params']["ResponseGroup"] = "Medium,Offers";
        $amazonCredentials['params']["IdType"] = "ISBN";
        $amazonCredentials['params']["SearchIndex"] = "All";

        $getUrl = $this->_getUrlWithSignature($amazonCredentials);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $getUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $xmlOutput  = curl_exec($ch);
        curl_close($ch);


        $booksArray = $this->_parseMultipleBooksAmazonXmlResponse($xmlOutput);

        $newBookArray = array();
        if(count($booksArray['books'])>0){
            foreach ($booksArray['books'] as $book) {
                if (strcmp($book['bookBinding'], "Kindle Edition") && strcmp($book['bookPriceAmazon'], "Not Found")) {

                    //Title Subtitle Formatting
                    if (strpos($book['bookTitle'], ":")) {
                        $book['bookSubTitle'] = substr($book['bookTitle'], strpos($book['bookTitle'], ":") + 2);
                        $book['bookTitle'] = substr($book['bookTitle'], 0, strpos($book['bookTitle'], ":"));
                    }
                    array_push($newBookArray, $book);
                }
            }

        }else{
            $booksArray = $this->_getGoogleBooks($isbn, 1,1);
            if (count($booksArray['books']) > 0){
                $newBookArray = $booksArray['books'];
            }
        }

        //Getting Lowest Price In campus
        if(count($newBookArray)>0){
            $userCampusId = $this->container->get('security.token_storage')->getToken()->getUser()->getCampus()->getId();
            $em = $this->getDoctrine()->getManager();
            $bookDealRepo=$em->getRepository('AppBundle:BookDeal');
            $lowestPriceOnCampus = $bookDealRepo->getLowestDealPriceInCampus($userCampusId,$newBookArray[0]['bookIsbn']);

            if($lowestPriceOnCampus[0][1]!=null){
                $newBookArray[0]['campusLowestPrice']= "$".number_format(floatval($lowestPriceOnCampus[0][1]),2);

            }
        }

        $booksArray['books'] = $newBookArray;


        return $this->_createJsonResponse('success', array('successData' => $booksArray), 200);

    }

    public function _addToCartAmazonUrl($bookOfferId)
    {
        $amazonSearchParams = $this->_getAmazonSearchParams();
        $amazonSearchParams['params']['Operation'] = "CartCreate";
        $amazonSearchParams['params']['Item.1.OfferListingId'] = $bookOfferId;
        $amazonSearchParams['params']['Item.1.Quantity'] = "1";

        $cartUrl = $this->_getUrlWithSignature($amazonSearchParams);
        return $cartUrl;
    }

    public function _getBooksByIsbnCampusBooks($isbn)
    {
        $campusBooksApiInfo = $this->getParameter('campus_books_api_info');
        $apiKey = $campusBooksApiInfo['api_key'];
        $host = $campusBooksApiInfo['host'];
        $uri = $campusBooksApiInfo['uri'];

       $url = $host . $uri . "?key=" . $apiKey . "&isbn=" . $isbn . "&format=json";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $jsonOutput  = curl_exec($ch);
        curl_close($ch);

        $arrayData = (json_decode($jsonOutput, true));

        if (array_key_exists('status', $arrayData['response'])) {
            if (!strpos($arrayData['response']['status'], 'error')) {
                return $this->_createJsonResponse('error', array('errorTitle' => "No Online Book Deal was found"), 400);
            }
        } else {
            return $this->_createJsonResponse('success', array('successData' => $arrayData), 200);
        }


    }

    public function _getBooksLowestPriceByIsbnCampusBooks($isbn)
    {
        $campusBooksApiInfo = $this->getParameter('campus_books_api_info_lowest_price');
        $apiKey = $campusBooksApiInfo['api_key'];
        $host = $campusBooksApiInfo['host'];
        $uri = $campusBooksApiInfo['uri'];

        $url = $host . $uri . "?key=" . $apiKey . "&isbn=" . $isbn;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $xmlOutput  = curl_exec($ch);
        curl_close($ch);


        $fileContents = str_replace(array("\n", "\r", "\t"), '', $xmlOutput);

        $simpleXml = simplexml_load_string($fileContents);
        $priceArray = array();
        if($simpleXml!=null && $simpleXml->page!=null && $simpleXml->page->offers!=null && $simpleXml->page->offers->condition!=null ) {
            foreach ($simpleXml->page->offers->condition as $condition) {
                foreach ($condition->offer as $offer) {
                    if ((string)$offer->condition_text[0] === "New" || (string) $offer->condition_text[0] === "International" || (string)$offer->condition_text[0] === "Used") {
                        array_push($priceArray, (floatval($offer->total_price[0])));
                    }
                }

            }
            if (count($priceArray) > 0){
                return "$" . number_format(floatval(min($priceArray)), 2);
            }
        }
        return false;
    }

    public function _getUrlWithSignature($amazonCredentials)
    {
        // sort the parameters
        ksort($amazonCredentials['params']);
        // create the canonicalization  query
        $canonicalizedQuery = array();
        foreach ($amazonCredentials['params'] as $param => $value) {
            $param = str_replace("%7E", "~", rawurlencode($param));
            $value = str_replace("%7E", "~", rawurlencode($value));
            $canonicalizedQuery[] = $param . "=" . $value;
        }
        $canonicalizedQuery = implode("&", $canonicalizedQuery);

        // create the string to sign
        $string_to_sign = $amazonCredentials['apiInfo']['method'] . "\n" . $amazonCredentials['apiInfo']['host'] . "\n" . $amazonCredentials['apiInfo']['uri'] . "\n" . $canonicalizedQuery;

        // calculate HMAC with SHA256 and base64-encoding
        $signature = base64_encode(hash_hmac("sha256", $string_to_sign, $amazonCredentials['apiInfo']['privateKey'], true));

        // encode the signature for the request
        $signature = str_replace("%7E", "~", rawurlencode($signature));
        $url = "http://" . $amazonCredentials['apiInfo']['host'] . $amazonCredentials['apiInfo']['uri'] . "?" . $canonicalizedQuery . "&Signature=" . $signature;

        return $url;
    }

    public function _getAmazonSearchParams()
    {


        $amazonApiInfo = $this->getParameter('amazon_api_info');

        $apiInfo = array();
        $apiInfo['method'] = $amazonApiInfo['method'];
        $apiInfo['host'] = $amazonApiInfo['host'];
        $apiInfo['uri'] = $amazonApiInfo['uri'];
        $apiInfo['privateKey'] = $amazonApiInfo['private_key'];


        $params = array();

        $params["AWSAccessKeyId"] = $amazonApiInfo['aws_access_key_id'];
        $params["AssociateTag"] = $amazonApiInfo['associate_tag'];
        $params["Service"] = "AWSECommerceService";
        $params["Timestamp"] = gmdate("Y-m-d\TH:i:s\Z");
        $params["Version"] = $amazonApiInfo['version'];
        $params["Power"] = "binding:hardcover or library or paperback";
        $params['Condition'] = "New";
        $params['MerchantId'] = 'All';

        return array(
            'apiInfo' => $apiInfo,
            'params' => $params
        );

    }

    public function _parseMultipleBooksAmazonXmlResponse($xml)
    {

        $fileContents = str_replace(array("\n", "\r", "\t"), '', $xml);

        $fileContents = trim(str_replace('"', "'", $fileContents));

        $simpleXml = simplexml_load_string($fileContents);

        $booksArray = array();

        if ($simpleXml != null && count($simpleXml->Items->Item)>0) {
            foreach ($simpleXml->Items->Item as $item) {
                $book = $this->_createJsonFromItemAmazon($item);
                if($book['bookIsbn']!='' && strcmp($book['bookPriceAmazon'],"Not Found")){
                    array_push($booksArray,$book);
                }

            }
            $totalSearchResults = (string)$simpleXml->Items->TotalResults;
        }else{
            $totalSearchResults = 0;
        }


        return array(
            'books' => $booksArray,
            'totalSearchResults' => $totalSearchResults
        );

    }

    public function _createJsonFromItemAmazon($item)
    {

        //Getting Price
        if (!empty($item->Offers->Offer->OfferListing->Price->FormattedPrice)) {
            $price = (string)$item->Offers->Offer->OfferListing->Price->FormattedPrice;
        } elseif (!empty($item->ListPrice->FormattedPrice)) {
            $price = (string)$item->ListPrice->FormattedPrice;
        } else {
            $price = "Not Found";
        }

        //Getting Author
        if (isset($item->ItemAttributes->Director)) {
            $book_director_author_artist = (string)$item->ItemAttributes->Director;
        } elseif (isset($item->ItemAttributes->Author)) {
            $book_director_author_artist = (string)$item->ItemAttributes->Author;
        } elseif (isset($item->ItemAttributes->Artist)) {
            $book_director_author_artist = (string)$item->ItemAttributes->Artist;
        } else {
            $book_director_author_artist = 'No Author Found';
        }

        //Getting offer
        if (!empty($item->Offers->Offer->OfferListing->OfferListingId)) {
            $offerId = (string)$item->Offers->Offer->OfferListing->OfferListingId;
        } else {
            $offerId = "";
        }


        //Getting Image
        if (!empty($item->MediumImage->URL)) {
            $book_image_medium_url = (string)$item->MediumImage->URL;
        } else {
            $book_image_medium_url = $_SERVER['REQUEST_SCHEME']."://".$_SERVER['HTTP_HOST'].$_SERVER['BASE'].'/assets/images/no_image.jpg';
        }

        if (!empty($item->LargeImage->URL)) {
            $book_image_large_url = (string)$item->LargeImage->URL;
        } else {
            $book_image_large_url = $_SERVER['REQUEST_SCHEME']."://".$_SERVER['HTTP_HOST'].$_SERVER['BASE'].'/assets/images/no_image.jpg';
        }

        //Getting Description
        if (!empty($item->EditorialReviews->EditorialReview->Content)) {
            $description = (string)$item->EditorialReviews->EditorialReview->Content;
        } else {
            $description = '';
        }


        return array(
            'bookAsin' => (string)$item->ASIN,
            'bookTitle' => (string)$item->ItemAttributes->Title,
            'bookDirectorAuthorArtist' => $book_director_author_artist,
            'bookPriceAmazon' => $price,
            'bookIsbn' => (string)$item->ItemAttributes->ISBN,
            'bookEan' => (string)$item->ItemAttributes->EAN,
            'bookEdition' => (string)$item->ItemAttributes->Edition,
            'bookPublisher' => (string)$item->ItemAttributes->Publisher,
            'bookPublishDate' => (string)$item->ItemAttributes->PublicationDate,
            'bookBinding' => (string)$item->ItemAttributes->Binding,
            'bookImages' => [
                array(
                    'image' => $book_image_large_url,
                    'imageId' => 0
                )
            ],
            'bookDescription' => $description,
            'bookPages' => (string)$item->ItemAttributes->NumberOfPages,
            'bookOfferId' => $offerId,
//            'bookLanguage'=> (string)$item->ItemAttributes->Languages->Language->Name,
        );
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

    public function _headerTokenDecode($headerData)
    {
        $webAppConfig = $this->getParameter('web_app_config');
        $mobileAppConfig = $this->getParameter('mobile_device_config');
        if(!strcmp($headerData['request-source'][0],$webAppConfig['source_type'])){
            return !strcmp($headerData['header-token'][0], hash_hmac("sha256", base64_encode($headerData['timestamp'][0]), $webAppConfig['api_key']))?true:false;
        }else if(!strcmp($headerData['request-source'][0],$mobileAppConfig['source_type'])){
           return !strcmp($headerData['header-token'][0], (md5(md5($headerData['timestamp'][0]).$mobileAppConfig['api_key'])))?true:false;

        }else{
            return false;
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
