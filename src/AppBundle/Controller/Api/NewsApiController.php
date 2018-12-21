<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\Book;
use AppBundle\Entity\Campus;
use AppBundle\Entity\Contact;
use AppBundle\Entity\News;
use AppBundle\Entity\Quote;
use AppBundle\Form\Type\BookDealType;
use AppBundle\Form\Type\ContactType;
use AppBundle\Form\Type\NewsType;
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

class NewsApiController extends Controller
{


    /**
     * Get News For Public
     */
    public function getActiveNewsAction(Request $request){

            $content = $request->getContent();
            $data = json_decode($content, true);
            $em = $this->getDoctrine()->getManager();
            $newsRepo=$em->getRepository('AppBundle:News');

            $pageSize = $data["pageSize"];
            $searchQuery = filter_var($data["searchQuery"], FILTER_SANITIZE_STRING);
            $pageNumber = $data["pageNumber"];
            $sort = $data["sort"];

            $totalNumber = $newsRepo->getAllActiveNewsSearchNumber($searchQuery);
            $searchResults= $newsRepo->getAllActiveNewsSearchResult($searchQuery, $pageNumber, $pageSize,$sort);

            $newsData = array();
            foreach($searchResults as $news){
                $news['newsDateTime']=$news['newsDateTime']->format('d M Y');
                $images = $newsRepo->findOneById($news['newsId'])->getNewsImages();

                $news['newsImages']=array();
                foreach($images as $image){
                    array_push($news['newsImages'], array(
                        'imageId'=>$image->getId(),
                        'image'=>$image->getNewsImageUrl()
                    ));
                }

                array_push($newsData,$news);
            }


            $data = array(
                'totalNews' => $newsData ,
                'totalNumber' => $totalNumber
            );

            return $this->_createJsonResponse('success', array('successData'=>array('news'=>$data)), 200);

    }

    /**
     * Get Single News For Public
     */
    public function getSingleNewsAction(Request $request){

        $content = $request->getContent();
        $data = json_decode($content, true);
        $em = $this->getDoctrine()->getManager();
        $newsRepo=$em->getRepository('AppBundle:News');


        $newsEntity  = $newsRepo->findOneById($data['newsId']);
        if(!strcmp($newsEntity->getNewsStatus(),"Activated")){
            $news=array();
            $news['newsTitle']=$newsEntity->getNewsTitle();
            $news['newsDescription']=$newsEntity->getNewsDescription();
            $news['newsDateTime']=$newsEntity->getNewsDateTime()->format('d M Y');
            $news['newsType']=$newsEntity->getNewsType();
            $news['newsVideoEmbedCode']=$newsEntity->getNewsVideoEmbedCode();
            $news['newsImages']=array();
            foreach($newsEntity->getNewsImages() as $image){
                array_push($news['newsImages'], array(
                    'imageId'=>$image->getId(),
                    'image'=>$image->getNewsImageUrl()
                ));
            }
        }

        return $this->_createJsonResponse('success', array('successData'=>array('news'=>$news)), 200);

    }

    public function _createJsonResponse($key, $data, $code)
    {
        $serializer = $this->container->get('jms_serializer');
        $json = $serializer->serialize([$key => $data], 'json');
        $response = new Response($json, $code);
        return $response;
    }


}
