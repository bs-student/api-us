<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\Book;
use AppBundle\Entity\Campus;
use AppBundle\Entity\Contact;
use AppBundle\Entity\Quote;
use AppBundle\Form\Type\BookDealType;
use AppBundle\Form\Type\ContactType;
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

class QuoteApiController extends Controller
{


    /**
     * Get Activated Student Quotes
     */
    public function getActivatedStudentQuotesAction(Request $request){

        $em = $this->getDoctrine()->getManager();
        $quoteRepo=$em->getRepository('AppBundle:Quote');
        $quotes = $quoteRepo->getActivatedStudentQuotes();

        return $this->_createJsonResponse('success', array('successData'=>$quotes), 200);

    }


    /**
     * Get Activated University Quotes
     */
    public function getActivatedUniversityQuotesAction(Request $request){

        $em = $this->getDoctrine()->getManager();
        $quoteRepo=$em->getRepository('AppBundle:Quote');
        $quotes = $quoteRepo->getActivatedUniversityQuotes();

        return $this->_createJsonResponse('success', array('successData'=>$quotes), 200);

    }


    public function _createJsonResponse($key, $data, $code)
    {
        $serializer = $this->container->get('jms_serializer');
        $json = $serializer->serialize([$key => $data], 'json');
        $response = new Response($json, $code);
        return $response;
    }


}
