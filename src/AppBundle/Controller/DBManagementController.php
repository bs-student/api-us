<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 */

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Controller\ChangePasswordController as BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller managing the password change
 *
 */
class DBManagementController extends Controller
{

    /**
     * Get Old Campuses and put to new one
     */
    public function addCampusesFromOldToNewAction()
    {

        $em = $this->getDoctrine()->getManager();
        $connection = $em->getConnection();
        $statement = $connection->prepare(
            'INSERT INTO `campuses`(`id`,`university_id`, `state_id`, `campus_name`)
            SELECT
            gl_subsystem.subsystemID,
            gl_subsystem.topsystemIDFK,
            states.id as state_id,
            gl_subsystem.subsystem_name
            FROM gl_subsystem
            LEFT JOIN states ON gl_subsystem.state = states.state_short_name'
        );
        $statement->execute();
        $affected_rows = $statement->rowCount();
        echo $affected_rows . " rows have been affected";
        die();

//        $result = $statement->fetchAll(); // note: !== $connection->fetchAll()!


    }


    /**
     * Get Old Books and put to new one
     */
    public function addBooksFromOldToNewAction()
    {

        $em = $this->getDoctrine()->getManager();
        $connection = $em->getConnection();
        $statement = $connection->prepare(
            'INSERT INTO `books`(`id`,`book_title`, `book_director_author_artist`,
			`book_edition`,`book_isbn10`,`book_isbn13`,`book_publisher`,
			`book_publish_date`,`book_binding`,`book_page`,`book_language`,
			`book_image`,`book_amazon_price`)
            SELECT
            gl_book.bookID,
            gl_book.book_title,
            gl_book.book_director_author_artist,
			gl_book.book_edition,
			gl_book.book_isbn_10,
			gl_book.book_isbn_13,
			gl_book.book_publisher,
			gl_book.book_publisher_date,
			gl_book.book_binding,
			gl_book.book_page,
			gl_book.book_language,
			gl_book.book_image_large_url,
			gl_book.book_price_amazon_new

            FROM gl_book limit 9000 offset 6501'
        );
        $statement->execute();
        $affected_rows = $statement->rowCount();
        echo $affected_rows . " rows have been affected";
        die();

//        $result = $statement->fetchAll(); // note: !== $connection->fetchAll()!


    }


    /**
     * Get Old Users and put to new one
     */
    public function addUsersFromOldToNewAction()
    {

        $em = $this->getDoctrine()->getManager();
        $connection = $em->getConnection();

        $statement = $connection->prepare(
            'INSERT INTO `users`(`id`,`campus_id`, `username`,`username_canonical`, `email`,`email_canonical`,`enabled`,`salt`,`password`,`locked`,`expired`,
			`roles`,`credentials_expired`,`full_name`,`registration_status`,`admin_approved`)
            SELECT
            gl_info_members.memberID,
            gl_info_members.subsystemIDFK,
            gl_info_members.screenname,
			LOWER(gl_info_members.screenname),
			gl_info_members.email,
			LOWER(gl_info_members.email),
			gl_info_members.activ,
			gl_info_members.password_salt,
			gl_info_members.password,
			0,
			0,
			(case when (gl_info_members.adminfunction = "Admin")
                 THEN
                      \'a:2:{i:0;s:16:"ROLE_NORMAL_USER";i:1;s:15:"ROLE_ADMIN_USER";}\'
                 ELSE
                      \'a:1:{i:0;s:16:"ROLE_NORMAL_USER";}\'
                 END),
            0,
            gl_info_members.fullname,
            "complete",
            (case when (gl_info_members.approved = 1)
                 THEN
                      "Yes"
                 ELSE
                      "No"
                 END)

            FROM gl_info_members limit 5000 offset 5954'
        );
        $statement->execute();
        $affected_rows = $statement->rowCount();
        echo $affected_rows . " rows have been affected";
        die();

//        $result = $statement->fetchAll(); // note: !== $connection->fetchAll()!


    }

    public function addBookDealsFromOldToNewAction()
    {
        $em = $this->getDoctrine()->getManager();
        $connection = $em->getConnection();

        $statement = $connection->prepare(
            'INSERT INTO `book_deals`(`id`,`book_id`, `seller_id`,`book_price_sell`,
			`book_condition`,`book_is_highlighted`,`book_has_notes`,
			`book_comment`,`book_contact_method`,`book_contact_home_number`,`book_contact_cell_number`,
			`book_contact_email`,`book_is_available_public`,`book_payment_method_cash_on_exchange`,
			`book_payment_method_cheque`,`book_available_date`,`book_selling_status`,`book_view_count`,
			`book_status`,`book_submitted_date_time`)
            SELECT
            gl_sell_book.book_sellID,
            gl_sell_book.bookIDFK,
            gl_sell_book.memberIDFK,
			gl_sell_book.price,

			(CASE
                WHEN gl_sell_book.book_condition = "barely_used" THEN "Barely Used"
                WHEN gl_sell_book.book_condition = "heavy_used" THEN "Heavily Used"
                WHEN gl_sell_book.book_condition = "new" THEN "New"
                WHEN gl_sell_book.book_condition = "used" THEN "Used"
                ELSE 1
            END),

			(case
			    when (gl_sell_book.book_highlight = 0)
                THEN
                  "No"
                ELSE
                  "Yes"
              END),
             (case when (gl_sell_book.book_notes = 0)
             THEN
                  "No"
             ELSE
                  "Yes"
             END),

			gl_sell_book.comments,

			(CASE
                WHEN gl_sell_book.cnt_method = "buyer_to_seller" THEN "buyerToSeller"
                WHEN gl_sell_book.cnt_method = "seller_to_buyer" THEN "sellerToBuyer"
                ELSE 1
            END),

			gl_sell_book.cnt_home_phone,
			gl_sell_book.cnt_cell_phone,
			gl_sell_book.cnt_email,

			(case when (gl_sell_book.available_public = 0)
                 THEN
                      "No"
                 ELSE
                      "Yes"
                 END),

			gl_sell_book.terms_method_cash,
			gl_sell_book.terms_method_check,
			gl_sell_book.available_date,

			"Selling",

			gl_sell_book.visit,
			(case when (gl_sell_book.active = 0)
                 THEN
                      "Deactivated"
                 ELSE
                      "Activated"
                 END),
			gl_sell_book.cleanup_notification_last_contact_deal

            FROM gl_sell_book limit 5000 offset 7135'
        );
        $statement->execute();
        $affected_rows = $statement->rowCount();
        echo $affected_rows . " rows have been affected";
        die();
    }

    public function addContactsFromOldToNewAction()
    {
        $em = $this->getDoctrine()->getManager();
        $connection = $em->getConnection();

        $statement = $connection->prepare(
            'INSERT INTO `contact`(`id`,`book_deal_id`, `buyer_id`,
			`buyer_email`,`buyer_home_phone`,`buyer_cell_phone`,
			`contact_datetime`,`sold_to_that_buyer`)
            SELECT
            gl_sell_contact.sell_contactID,
            gl_sell_contact.book_sellIDFK,
			(case when (gl_sell_contact.contact_memberIDFK = 0)
                 THEN
                      NULL
                 ELSE
                      gl_sell_contact.contact_memberIDFK
                 END),

			gl_sell_contact.cnt_email,
			gl_sell_contact.cnt_home_phone,
			gl_sell_contact.cnt_cell_phone,
			gl_sell_contact.contact_date,
			"No"

            FROM gl_sell_contact limit 5000 offset 120'
        );
        $statement->execute();
        $affected_rows = $statement->rowCount();
        echo $affected_rows . " rows have been affected";
        die();
    }

    public function addMessagesFromOldToNewAction()
    {
        $em = $this->getDoctrine()->getManager();
        $connection = $em->getConnection();

        $statement = $connection->prepare(
            'INSERT INTO `messages`(`user_id`, `contact_id`,
			`message_body`,`message_type`,`message_datetime`)
            SELECT

			(case when (gl_sell_contact.contact_memberIDFK = 0)
                 THEN
                      NULL
                 ELSE
                      gl_sell_contact.contact_memberIDFK
                 END),

            gl_sell_contact.sell_contactID,
            gl_sell_contact.cnt_message,
			"BuyerToSellerMessage",
			gl_sell_contact.contact_date

            FROM gl_sell_contact limit 5000 offset 120'
        );
        $statement->execute();
        $affected_rows = $statement->rowCount();
        echo $affected_rows . " rows have been affected";
        die();
    }

    public function getBookImagesFromAmazonAction()
    {
        $fileDir = '/../web/bookImages/';
        $fileNameDir = '/bookImages/';
        $fileDirHost = $this->container->getParameter('kernel.root_dir');

        $em = $this->getDoctrine()->getManager();
        $connection = $em->getConnection();

        $statement = $connection->prepare(
            'SELECT
              id,book_image
            FROM books limit 1000 OFFSET 6501'
        );
        $statement->execute();
        $result = $statement->fetchAll();

//        $query = 'UPDATE books SET book_image = CASE';

        foreach ($result as $row) {
            if ($row['book_image'] == null) {
                //Book Image Not Found
                $row['book_image'] = $fileNameDir . "no_image.jpg";

                var_dump(">>>>>>>>>>>>>>>>>>>>>>>>>>>>");
                echo "<br/>";
                var_dump($row['book_image']);
                echo "<br/>";
            } else {
                //Curl for Image
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $row['book_image']);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                $imageOutput = curl_exec($ch);
                curl_close($ch);

                if (strpos($imageOutput, 'Not Found') !== false || $imageOutput == '') {
                    //No Image Found
                    $row['book_image'] = $fileNameDir . "no_image.jpg";

                    var_dump(">>>>>>>>>>>>>>>>>>>>>>>>>>>>");
                    echo "<br/>";
                } else {

                    //Image Found
                    $fileSaveName = gmdate("Y-d-m_h_i_s_") . rand(0, 99999999) . ".jpg";
                    $fp = fopen($fileDirHost . $fileDir . $fileSaveName, 'x');
                    fwrite($fp, $imageOutput);
                    fclose($fp);
                    $row['book_image'] = $fileNameDir . $fileSaveName;
                }
                var_dump($row['book_image']);
                echo "<br/>";

            }

            $fa = fopen($fileDirHost . $fileDir . "file.txt", 'a+');
            fwrite($fa, $row['id'] . "," . $row['book_image'] . "\r\n");
            fclose($fa);
        }


        die();

    }


    public function updateBookTableForPicturesAction()
    {
        $fileDir = '/../web/bookImages/';
        $fileDirHost = $this->container->getParameter('kernel.root_dir');

        $fa = fopen($fileDirHost . $fileDir . "file.txt", 'r');

        $query = 'UPDATE books SET book_image = CASE <br/>';

        while (!feof($fa)) {
            $line = fgets($fa);
            $id = substr($line, 0, strpos($line, ","));
            $bookImage = substr($line, strpos($line, ",") + 1, strpos($line, "\r\n") - 1);
//            var_dump($id);
//            var_dump($bookImage);

            $query .= ' WHEN id = ' . $id . ' THEN "' . $bookImage . '" <br/>';

        }
        $query .= '<br/> ELSE book_image <br/>
            END;';
        echo($query);
        die();
    }

    public function getImagesFromAmazonAction()
    {


        $data = file_get_contents('./assets/books.json');
        $json = json_decode($data, TRUE);


        $amazonCredentials = $this->_getAmazonSearchParams();

        $amazonCredentials['params']['Operation'] = "ItemLookup";

        $amazonCredentials['params']["ResponseGroup"] = "Images";


        foreach ($json as $row) {
            if ($row['book_image'] != "/bookImages/no_image.jpg") {
                $amazonCredentials['params']["ItemId"] = $row['book_isbn10'];
                $getUrl = $this->_getUrlWithSignature($amazonCredentials);

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $getUrl);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                $xmlOutput = curl_exec($ch);
                curl_close($ch);
                $booksLargeImage = $this->_parseMultipleBooksAmazonXmlResponse($xmlOutput);
                if ($booksLargeImage) {


                    $fa = fopen("./bookImages/new/file.txt", 'a+');
                    fwrite($fa, $row['id'] . "===" . $row['book_image'] . "===" . $booksLargeImage . "\r\n");
                    fclose($fa);

//                    //Curl for Image
//                    $ch = curl_init();
//                    curl_setopt($ch, CURLOPT_URL, $booksLargeImage);
//                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
//                    $imageOutput = curl_exec($ch);
//                    curl_close($ch);
//
//
//                    if(strpos($imageOutput,'Not Found')!==false || $imageOutput==''){
//
//                        $fx = fopen("./bookImages/new/file_not_found.txt", 'a+');
//                        fwrite($fx,$json[$i]['book_image']."\r\n");
//                        fclose($fx);
//
//                    }else{
//                        //Image Found
//
//                        $fp = fopen(".".$json[$i]['book_image'], 'x');
//                        fwrite($fp, $imageOutput);
//                        fclose($fp);
//
//                        $fa = fopen("./bookImages/new/file.txt", 'a+');
//                        fwrite($fa,$json[$i]['book_image']."\r\n");
//                        fclose($fa);
//
//                    }


                } else {
                    $fk = fopen("./bookImages/new/file_could_not_get_link.txt", 'a+');
                    fwrite($fk, $row['id'] . "===" . $row['book_isbn10'] . "===" . $row['book_image'] . "\r\n");
                    fclose($fk);

                }

            } else {
                $fy = fopen("./bookImages/new/file_was_not_there.txt", 'a+');
                fwrite($fy, $row['id'] . "===" . $row['book_isbn10'] . " = " . $row['book_image'] . "\r\n");
                fclose($fy);
            }
        }

        var_dump("Done");


//        echo ('<pre> print the json ');
//        print_r ($json);
//        echo ('</pre>');
//        var_dump($json);
        die();
    }

    public function getFailedImageLinkAction()
    {


        $fa = fopen("./assets/file_could_not_get_link_test.txt", 'r');
        $amazonCredentials = $this->_getAmazonSearchParams();

        $amazonCredentials['params']['Operation'] = "ItemLookup";

        $amazonCredentials['params']["ResponseGroup"] = "Images";

        while (!feof($fa)) {
            $line = fgets($fa);
            $array = explode("===", $line);


            if (count($array) == 3) {

                $amazonCredentials['params']["ItemId"] = $array[1];
                $getUrl = $this->_getUrlWithSignature($amazonCredentials);

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $getUrl);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                $xmlOutput = curl_exec($ch);
                curl_close($ch);


                $booksLargeImage = $this->_parseMultipleBooksAmazonXmlResponse($xmlOutput);
                if ($booksLargeImage) {
                    $fx = fopen("./assets/file_newly_link_found.txt", 'a+');
                    fwrite($fx, $array[0] . "===" . substr($array[2], 0, strlen($array[2]) - 1) . "===" . $booksLargeImage . "\r\n");
                    fclose($fx);
                } else {
                    $fk = fopen("./assets/file_newly_link_not_found.txt", 'a+');
                    fwrite($fk, $array[0] . "===" . $array[1] . "===" . substr($array[2], 0, strlen($array[2]) - 1) . "\r\n");
                    fclose($fk);

                }

            } else {
                $fw = fopen("./assets/file_newly_link_tried_asin_not_found.txt", 'a+');
                fwrite($fw, $array[0] . "===" . $array[1] . "===" . substr($array[2], 0, strlen($array[2]) - 1) . "\r\n");
                fclose($fw);
            }


        }


        die();
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

    public function _parseMultipleBooksAmazonXmlResponse($xml)
    {

        $fileContents = str_replace(array("\n", "\r", "\t"), '', $xml);
        $fileContents = trim(str_replace('"', "'", $fileContents));
        $simpleXml = simplexml_load_string($fileContents);

        echo "<pre>";
        print_r(($simpleXml));
        echo "</pre>";
        die();

        if (!empty($simpleXml->Items)) {

            if (!empty($simpleXml->Items->Item)) {

                foreach ($simpleXml->Items->Item as $item) {

                    if (!empty($item->Offers)) {

                        if (!empty($item->Offers->Offer)) {

                            if (!empty($item->Offers->Offer->OfferListing)) {

                                if (!empty($item->Offers->Offer->OfferListing->Price)) {

                                    if (!empty($item->Offers->Offer->OfferListing->Price->FormattedPrice)) {

                                        return substr((string)$item->Offers->Offer->OfferListing->Price->FormattedPrice, 1);
//                                                echo "<pre>";
//                                                print_r(substr((string)$item->Offers->Offer->OfferListing->Price->FormattedPrice,1));
//
//                                                die();
                                    } else {
                                        continue;
                                    }
                                } else {
                                    continue;
                                }
                            } else {

                                continue;
                            }
                        } else {
                            continue;
                        }
                    } else {
                        continue;
                    }
                }
            } else {

                return false;
            }
        } else {
            return false;
        }
        return false;
//        echo "<pre>";
//        print_r($simpleXml);
//        echo "</pre>";
//        die();
//        $item =$simpleXml->Items->Item->OfferSummary->LowestNewPrice->FormattedPrice;


        if ($simpleXml != null) {

            if ($simpleXml->Items != null) {

                if ($simpleXml->Items->Item != null) {

                    if ($simpleXml->Items->Item->OfferSummary != null) {
                        echo "<pre>";
                        print_r($simpleXml->Items);
                        echo "</pre>";
                        die();
                        if ($simpleXml->Items->Item->OfferSummary->LowestNewPrice != null) {

                            if ($simpleXml->Items->Item->OfferSummary->FormattedPrice != null) {
                                $price = $simpleXml->Items->Item->OfferSummary->FormattedPrice;
                                var_dump($price);
                                die();
                                return (substr((string)$price[0], 1));
                            } else {
                                return false;
                            }
                        } else {
                            return false;
                        }
                    } else {
                        return false;
                    }
                } else {
                    return false;
                }
            } else {
                return false;
            }

        } else {
            return false;
        }


//        return (substr((string)$item[0],1));


//        if($simpleXml!=null){
//
//            if($simpleXml->Items!=null){
//
//                if($simpleXml->Items->Item!=null){
//                    if($simpleXml->Items->Item->LargeImage!=null){
//                        return((string)$simpleXml->Items->Item->LargeImage->URL);
//                    }else{
//                        return false;
//                    }
//                }else{
//                    return false;
//                }
//            }else{
//                return false;
//            }
//
//        }else{
//            return false;
//        }

    }


    public function getRealImagesFromAmazonAction()
    {
        $fa = fopen("./assets/file_test.txt", 'r');


        while (!feof($fa)) {
            $line = fgets($fa);
            $array = explode("===", $line);


            if (count($array) == 3) {

                $link = (substr($array[2], 0, strlen($array[2]) - 2));

//                Curl for Image
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $link);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);

                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/53.0.2785.143 Safari/537.36',
                    'Accept-Encoding: gzip, deflate, sdch',
                    'Accept-Language: en-US,en;q=0.8',
                    'Host: ecx.images-amazon.com',
                    'Connection: keep-alive',
                    'Pragma: no-cache',
                    'Cache-Control: no-cache',
                    'Upgrade-Insecure-Requests: 1'
                ));
                $imageOutput = curl_exec($ch);
                curl_close($ch);

                if (strpos($imageOutput, 'Not Found') !== false || $imageOutput == '') {

                    $fx = fopen("./assets/image_not_found.txt", 'a+');
                    fwrite($fx, $array[0] . "===" . $array[1] . "===" . $link . "\r\n");
                    fclose($fx);

                } else {
                    //Image Found

                    $fp = fopen("." . $array[1], 'x');
                    fwrite($fp, $imageOutput);
                    fclose($fp);

                    $fq = fopen("./assets/image_file_found.txt", 'a+');
                    fwrite($fq, $array[0] . "===" . $array[1] . "===" . $link . "\r\n");
                    fclose($fq);

                }

            }


        }


        die();
    }


    public function getDatabaseBackupAction()
    {


        $this->get('backup_manager')->makeBackup()->run('development', array('local'), 'gzip');

        die();
    }

    public function getAmazonPricesAction(Request $request)
    {
        $json = file_get_contents('test.json');
        $arrayData = json_decode($json, true);

        $fx = fopen("sql.sql", 'a+');
        $fe = fopen("error.txt", 'a+');
        fwrite($fx, "UPDATE books SET book_amazon_price = CASE id \r\n");
        foreach ($arrayData as $row) {

            $amazonCredentials = $this->_getAmazonSearchParams();

//            $amazonCredentials['params']['Operation'] = "ItemLookup";
//            $amazonCredentials['params']["ItemId"] = $row['book_isbn10'] ? $row['book_isbn10'] : $row['book_isbn13'];
//            $amazonCredentials['params']["ResponseGroup"] = "Medium,Offers";
//            $amazonCredentials['params']["IdType"] = "ISBN";
//            $amazonCredentials['params']["SearchIndex"] = "All";

            $amazonCredentials['params']['Operation'] = "ItemSearch";
            $amazonCredentials['params']["ItemPage"] = 1;
            $amazonCredentials['params']["Keywords"] = $row['book_isbn10'] ? $row['book_isbn10'] : $row['book_isbn13'];
            $amazonCredentials['params']["SearchIndex"] = "Books";
            $amazonCredentials['params']["ResponseGroup"] = "Medium,Offers";


            $getUrl = $this->_getUrlWithSignature($amazonCredentials);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $getUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            $xmlOutput = curl_exec($ch);
            curl_close($ch);


            $booksArray = $this->_parseMultipleBooksAmazonXmlResponse($xmlOutput);
//            var_dump($booksArray);
//            die();
            if (($booksArray)) {
                fwrite($fx, " WHEN " . $row['id'] . " THEN " . $booksArray . " \r\n");
                echo "<pre>";
                print_r($booksArray);
                echo "</pre>";

            } else {
                fwrite($fe, json_encode($row). " \r\n");
            }


        }
        fwrite($fx, " ELSE book_amazon_price END");
        fclose($fx);
        fclose($fe);
        die();
    }


    public function updateRegistrationDateAction()
    {
        $fa = fopen("./assets/gl_info_members.txt", 'r');
        $fx = fopen("sql.sql", 'a+');

        fwrite($fx, "UPDATE users SET registration_date_time = CASE id \r\n");

        while (!feof($fa)) {
            $line = fgets($fa);
            $array = explode(",", $line);


            fwrite($fx, " WHEN " . substr($array[0],1) . " THEN " . substr($array[1],0,strlen($array[1])-2) . " \r\n");


//            echo "<pre>";
//            print_r(substr($array[0],1));
//            print_r(substr($array[1],0,strlen($array[1])-2));
//            echo "</pre>";
//            die();



        }

        fwrite($fx, " ELSE registration_date_time END");
        fclose($fx);
        fclose($fa);
        die();


    }

}
