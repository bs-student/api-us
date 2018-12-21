<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\Book;
use AppBundle\Entity\Campus;
use AppBundle\Entity\Contact;
use AppBundle\Entity\Log;
use AppBundle\Entity\Newsletter;
use AppBundle\Entity\Quote;
use AppBundle\Form\Type\BookDealType;
use AppBundle\Form\Type\ContactType;
use AppBundle\Form\Type\LogType;
use AppBundle\Form\Type\NewsletterType;
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
use Symfony\Component\HttpFoundation\StreamedResponse;

class NewsletterApiController extends Controller
{


    /**
     * Add Newsletter Email
     */
    public function addNewsletterEmailAction(Request $request){


        $content = $request->getContent();
        $data = json_decode($content, true);
        $em = $this->getDoctrine()->getManager();
        $newsletterRepo=$em->getRepository('AppBundle:Newsletter');

        if(array_key_exists('email',$data)){
            $newsletter = $newsletterRepo->findOneBy(array('email'=>$data['email']));
            if($newsletter instanceof Newsletter){
                if(strcmp($newsletter->getActivationStatus(),"Activated")){

                    //If Email exist and not Activated then Update (Activate)
                    $data['activationStatus']='Activated';
                    $data['lastUpdateDateTime']=gmdate('Y-m-d H:i:s');

                    $newsletterForm = $this->createForm(new NewsletterType(), $newsletter);

                    $newsletterForm->submit($data);

                    if ($newsletterForm->isValid()) {
                        $em->persist($newsletter);
                        $em->flush();
                        return $this->_createJsonResponse('success', array(
                            'successTitle' => "Email has been Subscribed"
                        ), 201);

                    } else {
                        return $this->_createJsonResponse('error', array("errorTitle"=>"Could Not Subscribe Your Email","errorData" => $newsletterForm), 400);
                    }

                }else{
                    // If Email Exist and Activated  then error
                    return $this->_createJsonResponse('error', array("errorTitle"=>"Your Email is Already Subscribed"), 400);
                }
            }else{

                // Add new Email to Newsletter
                $newsletter = new Newsletter();

                $data['activationStatus']='Activated';
                $data['lastUpdateDateTime']=gmdate('Y-m-d H:i:s');


                $newsletterForm = $this->createForm(new NewsletterType(), $newsletter);

                $newsletterForm->submit($data);

                if ($newsletterForm->isValid()) {
                    $em->persist($newsletter);
                    $em->flush();
                    return $this->_createJsonResponse('success', array(
                        'successTitle' => "Email has been Subscribed"
                    ), 201);

                } else {
                    return $this->_createJsonResponse('error', array("errorTitle"=>"Sorry, Could Not Subscribe Your Email","errorData" => $newsletterForm), 400);
                }
            }
        }else{
            return $this->_createJsonResponse('error', array("errorTitle"=>"Sorry, Could Not Subscribe Your Email","errorDescription" => "Check your form and submit again"), 400);
        }

    }


    /**
     * Get All Newsletter Emails
     */
    public function getAllNewsletterEmailsAction(Request $request){
        $user = $this->container->get('security.token_storage')->getToken()->getUser();

        if(in_array('ROLE_ADMIN_USER',$user->getRoles(),true)){

            $content = $request->getContent();
            $data = json_decode($content, true);
            $em = $this->getDoctrine()->getManager();
            $newsletterRepo=$em->getRepository('AppBundle:Newsletter');

            $pageSize = $data["pageSize"];
            $searchQuery = filter_var($data["searchQuery"], FILTER_SANITIZE_STRING);
            $pageNumber = $data["pageNumber"];
            $sort = $data["sort"];

            $totalNumber = $newsletterRepo->getAllNewsletterEmailSearchNumber($searchQuery);
            $searchResults= $newsletterRepo->getAllNewsletterEmailSearchResult($searchQuery, $pageNumber, $pageSize,$sort);

            $totalData=array();
            foreach($searchResults as $newsletter){
                $newsletter['lastUpdateDateTime'] =$newsletter['lastUpdateDateTime']->format('H:i d-M-Y');
                array_push($totalData,$newsletter);
            }


            $data = array(
                'totalNewsletterEmails' => $totalData,
                'totalNumber' => $totalNumber
            );

            return $this->_createJsonResponse('success', array('successData'=>array('newsletterEmails'=>$data)), 200);
        }else{
            return $this->_createJsonResponse('error', array('errorTitle'=>"You are not authorized to see this page."), 400);
        }
    }

    /**
     * Export All Newsletter Emails
     */
    public function exportAllNewsletterEmailsAction(Request $request){
        $user = $this->container->get('security.token_storage')->getToken()->getUser();

        if(in_array('ROLE_ADMIN_USER',$user->getRoles(),true)){

            $em = $this->getDoctrine()->getManager();
            $newsletterRepo=$em->getRepository('AppBundle:Newsletter');

            $handle = fopen('newsletterEmails/newsletterEmails.csv', 'w+');

            // Add the header of the CSV file
            fputcsv($handle, array('Email', 'DateTime', 'Status'),',');

            $emails = $newsletterRepo->getAllEmails();
            // Add the data queried from database
            foreach($emails as $email) {
                fputcsv(
                    $handle, // The file pointer
                    array($email['email'], $email['lastUpdateDateTime']->format('H:i d-M-Y'), $email['activationStatus']), // The fields
                    ',' // The delimiter
                );
            }

            fclose($handle);

            $logData = array(
                'user'=>$user->getId(),
                'logType'=>"Newsletter Export",
                'logDateTime'=>gmdate('Y-m-d H:i:s'),
                'logDescription'=> $user->getUsername()." has exported newsletter into a csv file.",
                'userIpAddress'=>$this->container->get('request')->getClientIp(),
                'logUserType'=> in_array("ROLE_ADMIN_USER",$user->getRoles())?"Admin User":"Normal User"
            );
            $this->_saveLog($logData);

            return $this->_createJsonResponse('success', array('successTitle'=>"CSV is generated",'successData'=>array("link"=>"/newsletterEmails/newsletterEmails.csv")), 200);

        }else{
            return $this->_createJsonResponse('error', array('errorTitle'=>"You are not authorized to see this page."), 400);
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

    public function _createJsonResponse($key, $data, $code)
    {
        $serializer = $this->container->get('jms_serializer');
        $json = $serializer->serialize([$key => $data], 'json');
        $response = new Response($json, $code);
        return $response;
    }


}
