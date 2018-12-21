<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Mailer;

use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Routing\RouterInterface;
use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Mailer\MailerInterface;
use FOS\UserBundle\Mailer\Mailer as BaseClass;

/**
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
class Mailer extends BaseClass
{



    public function sendConfirmationEmailMessage(UserInterface $user)
    {
        $message = \Swift_Message::newInstance();
        $url = $this->parameters['host_info']['host_url']."/confirmRegistration/".$user->getConfirmationToken();
        $data = array(
            'user' => $user->getUsername(),
            'confirmationUrl' =>  $url,
            'header_image'=>$message->embed(\Swift_Image::fromPath('assets/images/header_big.jpg')),
            'button_activate_account_image'=>$message->embed(\Swift_Image::fromPath('assets/images/activate_button.jpg')),
            'button_tell_friends_image'=>$message->embed(\Swift_Image::fromPath('assets/images/tell_friend_button.jpg')),
            'share_image'=>$message->embed(\Swift_Image::fromPath('assets/images/share.jpg')),
            'footer_image'=>$message->embed(\Swift_Image::fromPath('assets/images/footer.jpg')),
        );

        $message->setBody(
            $this->templating->render('mail_templates/registration_confirmation.html.twig',$data),'text/html'
        );

        $this->_sendMail($message,"Student2Student : Confirm Registration Process",$this->parameters['host_info']['no_reply_email'],$user->getEmail());
    }

    public function sendResettingEmailMessage(UserInterface $user)
    {

        $message = \Swift_Message::newInstance();
        $url = $this->parameters['host_info']['host_url']."/resetPassword/".$user->getConfirmationToken();
        $data = array(
            'user' => $user->getUsername(),
            'confirmationUrl' =>  $url,
            'header_image'=>$message->embed(\Swift_Image::fromPath('assets/images/header_big.jpg')),
            'button_reset_password_image'=>$message->embed(\Swift_Image::fromPath('assets/images/reset.jpg')),
            'button_tell_friends_image'=>$message->embed(\Swift_Image::fromPath('assets/images/tell_friend_button.jpg')),
            'share_image'=>$message->embed(\Swift_Image::fromPath('assets/images/share.jpg')),
            'footer_image'=>$message->embed(\Swift_Image::fromPath('assets/images/footer.jpg')),
        );

        $message->setBody(
            $this->templating->render('mail_templates/reset_mail.html.twig',$data),'text/html'
        );

        $this->_sendMail($message,"Student2Student : Reset Password",$this->parameters['host_info']['no_reply_email'],$user->getEmail());

    }

    public function operateContactMailingProcess($bookDeal,$book,$seller,$buyerInfo,$buyerMessage)
    {


        if(!strcmp($bookDeal->getBookContactMethod(),"buyerToSeller")){

            if(!strcmp("On",$seller->getEmailNotification())) {
                //Sending First mail
                $message1 = \Swift_Message::newInstance();

                $toSellerRendered = $this->templating->render("mail_templates/buyer_to_seller_method_to_seller_mail.html.twig", array(
                    'bookDeal' => $bookDeal,
                    'seller' => $seller,
                    'buyerInfo' => $buyerInfo,
                    'book' => $book,
                    'buyerMessage' => $buyerMessage,
                    'book_image' => $message1->embed(\Swift_Image::fromPath(substr($book->getBookImage(), 1))),

                    'header_image'=>$message1->embed(\Swift_Image::fromPath('assets/images/header.jpg')),
                    'footer_image'=>$message1->embed(\Swift_Image::fromPath('assets/images/footer.jpg')),
                    'envelop_image'=>$message1->embed(\Swift_Image::fromPath('assets/images/envelop.png'))

                ));

                $message1->setBody($toSellerRendered, 'text/html');
                $this->_sendMail($message1, "Student2Student : Buyer Contacting You", $this->parameters['host_info']['no_reply_email'], $seller->getEmail());
            }

            if($buyerInfo['buyerEntity'] instanceof User) {
                if (!strcmp("On", $buyerInfo['buyerEntity']->getEmailNotification())) {
                    //Sending 2nd Mail
                    $message2 = \Swift_Message::newInstance();
                    $toBuyerRendered = $this->templating->render("mail_templates/buyer_to_seller_method_to_buyer_mail.html.twig", array(
                        'bookDeal' => $bookDeal,
                        'seller' => $seller,
                        'buyerInfo' => $buyerInfo,
                        'book' => $book,
                        'buyerMessage' => $buyerMessage,
                        'book_image' => $message2->embed(\Swift_Image::fromPath(substr($book->getBookImage(), 1))),
                        'header_image'=>$message2->embed(\Swift_Image::fromPath('assets/images/header.jpg')),
                        'footer_image'=>$message2->embed(\Swift_Image::fromPath('assets/images/footer.jpg')),
                        'envelop_image'=>$message2->embed(\Swift_Image::fromPath('assets/images/envelop.png'))
                    ));
                    $message2->setBody($toBuyerRendered, 'text/html');
                    $this->_sendMail($message2, "Student2Student : Seller Contact Information ", $this->parameters['host_info']['no_reply_email'], $buyerInfo['buyerEmail']);
                }
            }else{
                $message2 = \Swift_Message::newInstance();
                $toBuyerRendered = $this->templating->render("mail_templates/buyer_to_seller_method_to_buyer_mail.html.twig", array(
                    'bookDeal' => $bookDeal,
                    'seller' => $seller,
                    'buyerInfo' => $buyerInfo,
                    'book' => $book,
                    'buyerMessage' => $buyerMessage,
                    'book_image' => $message2->embed(\Swift_Image::fromPath(substr($book->getBookImage(), 1))),
                    'header_image'=>$message2->embed(\Swift_Image::fromPath('assets/images/header.jpg')),
                    'footer_image'=>$message2->embed(\Swift_Image::fromPath('assets/images/footer.jpg')),
                    'envelop_image'=>$message2->embed(\Swift_Image::fromPath('assets/images/envelop.png'))
                ));
                $message2->setBody($toBuyerRendered, 'text/html');
                $this->_sendMail($message2, "Student2Student : Seller Contact Information ", $this->parameters['host_info']['no_reply_email'], $buyerInfo['buyerEmail']);
            }
        }elseif(!strcmp($bookDeal->getBookContactMethod(),"sellerToBuyer")){

            if(!strcmp("On",$seller->getEmailNotification())) {

                //Sending First Mail
                $message1 = \Swift_Message::newInstance();

                $toSellerRendered = $this->templating->render("mail_templates/seller_to_buyer_method_to_seller_mail.html.twig", array(
                    'bookDeal' => $bookDeal,
                    'seller' => $seller,
                    'buyerInfo' => $buyerInfo,
                    'book' => $book,
                    'buyerMessage' => $buyerMessage,
                    'book_image' => $message1->embed(\Swift_Image::fromPath(substr($book->getBookImage(), 1))),
                    'header_image'=>$message1->embed(\Swift_Image::fromPath('assets/images/header.jpg')),
                    'footer_image'=>$message1->embed(\Swift_Image::fromPath('assets/images/footer.jpg')),
                    'envelop_image'=>$message1->embed(\Swift_Image::fromPath('assets/images/envelop.png'))
                ));


                $message1->setBody($toSellerRendered, 'text/html');

                $this->_sendMail($message1, "Student2Student : Buyer Contact Information", $this->parameters['host_info']['no_reply_email'], $seller->getEmail());
            }

            if($buyerInfo['buyerEntity'] instanceof User) {
                if (!strcmp("On", $buyerInfo['buyerEntity']->getEmailNotification())) {
                    //Sending 2nd Mail
                    $message2 = \Swift_Message::newInstance();

                    $toBuyerRendered = $this->templating->render("mail_templates/seller_to_buyer_method_to_buyer_mail.html.twig", array(
                        'bookDeal' => $bookDeal,
                        'seller' => $seller,
                        'buyerInfo' => $buyerInfo,
                        'book' => $book,
                        'buyerMessage' => $buyerMessage,
                        'book_image' => $message2->embed(\Swift_Image::fromPath(substr($book->getBookImage(), 1))),
                        'header_image'=>$message1->embed(\Swift_Image::fromPath('assets/images/header.jpg')),
                        'footer_image'=>$message1->embed(\Swift_Image::fromPath('assets/images/footer.jpg')),
                        'envelop_image'=>$message1->embed(\Swift_Image::fromPath('assets/images/envelop.png'))
                    ));
                    $message2->setBody($toBuyerRendered, 'text/html');
                    $this->_sendMail($message2, "Student2Student : Sent Information to Seller", $this->parameters['host_info']['no_reply_email'], $buyerInfo['buyerEmail']);
                }
            }else{
                $message2 = \Swift_Message::newInstance();

                $toBuyerRendered = $this->templating->render("mail_templates/seller_to_buyer_method_to_buyer_mail.html.twig", array(
                    'bookDeal' => $bookDeal,
                    'seller' => $seller,
                    'buyerInfo' => $buyerInfo,
                    'book' => $book,
                    'buyerMessage' => $buyerMessage,
                    'book_image' => $message2->embed(\Swift_Image::fromPath(substr($book->getBookImage(), 1))),
                    'header_image'=>$message1->embed(\Swift_Image::fromPath('assets/images/header.jpg')),
                    'footer_image'=>$message1->embed(\Swift_Image::fromPath('assets/images/footer.jpg')),
                    'envelop_image'=>$message1->embed(\Swift_Image::fromPath('assets/images/envelop.png'))
                ));
                $message2->setBody($toBuyerRendered, 'text/html');
                $this->_sendMail($message2, "Student2Student : Sent Information to Seller", $this->parameters['host_info']['no_reply_email'], $buyerInfo['buyerEmail']);
            }
        }elseif(!strcmp($bookDeal->getBookContactMethod(),"student2studentBoard")){

            if(!strcmp("On",$seller->getEmailNotification())) {

                //Sending First Mail
                $message1 = \Swift_Message::newInstance();

                $toSellerRendered = $this->templating->render("mail_templates/student2studentBoard_method_to_seller_mail.html.twig", array(
                    'bookDeal' => $bookDeal,
                    'seller' => $seller,
                    'buyerInfo' => $buyerInfo,
                    'book' => $book,
                    'buyerMessage' => $buyerMessage,
                    'book_image' => $message1->embed(\Swift_Image::fromPath(substr($book->getBookImage(), 1))),
                    'header_image'=>$message1->embed(\Swift_Image::fromPath('assets/images/header.jpg')),
                    'footer_image'=>$message1->embed(\Swift_Image::fromPath('assets/images/footer.jpg')),
                    'envelop_image'=>$message1->embed(\Swift_Image::fromPath('assets/images/envelop.png'))
                ));
				$message1->setBody($toSellerRendered, 'text/html');

                $this->_sendMail($message1, "Student2Student : New Contact Received", $this->parameters['host_info']['no_reply_email'], $seller->getEmail());
            }

            if($buyerInfo['buyerEntity'] instanceof User) {
                if (!strcmp("On", $buyerInfo['buyerEntity']->getEmailNotification())) {
                    

                    //Sending 2nd Mail
                    $message2 = \Swift_Message::newInstance();

                    $toBuyerRendered = $this->templating->render("mail_templates/student2studentBoard_method_to_buyer_mail.html.twig", array(
                        'bookDeal' => $bookDeal,
                        'seller' => $seller,
                        'buyerInfo' => $buyerInfo,
                        'book' => $book,
                        'buyerMessage' => $buyerMessage,
                        'book_image' => $message2->embed(\Swift_Image::fromPath(substr($book->getBookImage(), 1))),
                        'header_image'=>$message2->embed(\Swift_Image::fromPath('assets/images/header.jpg')),
                        'footer_image'=>$message2->embed(\Swift_Image::fromPath('assets/images/footer.jpg')),
                        'envelop_image'=>$message2->embed(\Swift_Image::fromPath('assets/images/envelop.png'))
                    ));
                    $message2->setBody($toBuyerRendered, 'text/html');
                    $this->_sendMail($message2, "Student2Student : Contacted Seller ", $this->parameters['host_info']['no_reply_email'], $buyerInfo['buyerEmail']);
                }
            }else{
                $message2 = \Swift_Message::newInstance();

                $toBuyerRendered = $this->templating->render("mail_templates/student2studentBoard_method_to_buyer_mail.html.twig", array(
                    'bookDeal' => $bookDeal,
                    'seller' => $seller,
                    'buyerInfo' => $buyerInfo,
                    'book' => $book,
                    'buyerMessage' => $buyerMessage,
                    'book_image' => $message2->embed(\Swift_Image::fromPath(substr($book->getBookImage(), 1))),
                    'header_image'=>$message1->embed(\Swift_Image::fromPath('assets/images/header.jpg')),
                    'footer_image'=>$message1->embed(\Swift_Image::fromPath('assets/images/footer.jpg')),
                    'envelop_image'=>$message1->embed(\Swift_Image::fromPath('assets/images/envelop.png'))
                ));
                $message2->setBody($toBuyerRendered, 'text/html');
                $this->_sendMail($message2, "Student2Student : Contacted Seller ", $this->parameters['host_info']['no_reply_email'], $buyerInfo['buyerEmail']);
            }
        }

    }

    public function operateMessageMailingProcess($contact,$message,$messageType,$messageFinalArray){

        $buyerEntity = null;
        if($contact->getBuyer()!=null){
            $buyer = $contact->getBuyer()->getUsername();
            $buyerEntity = $contact->getBuyer();
        }else{
            $buyer = $contact->getBuyerNickName();
        }
        $data=array(
            'contact'=>$contact,
            'bookDeal' => $contact->getBookDeal(),
            'seller' => $contact->getBookDeal()->getSeller(),
            'buyer'=>$buyer,
            'buyerEntity'=>$buyerEntity,
            'book'=>$contact->getBookDeal()->getBook(),
            'message'=>$message,
            'messageArray'=>$messageFinalArray,
            'contactMethod'=>$contact->getBookDeal()->getBookContactMethod()
        );

        if(!strcmp($messageType,"buyerSendingToSeller")){
            $this->_buyerToSellerMessageMail($data);
        }elseif(!strcmp($messageType,"sellerSendingToBuyer")){
            $this->_sellerToBuyerMessageMail($data);
        }


    }

    public function _buyerToSellerMessageMail($data){

        $sellerMailAddress = $data['bookDeal']->getBookContactEmail()==''?$data['seller']->getEmail():$data['bookDeal']->getBookContactEmail();

        if(!strcmp("On",$data['seller']->getEmailNotification())){

            $message1 = \Swift_Message::newInstance();

            for($i=0;$i<count($data['messageArray']);$i++){
                $data['messageArray'][$i]['embedProfilePicture'] =$message1->embed(\Swift_Image::fromPath($data['messageArray'][$i]['senderProfilePicture']));
            }
            $data['book_image']=$message1->embed(\Swift_Image::fromPath(substr($data['book']->getBookImage(),1)));
            $data['header_image']=$message1->embed(\Swift_Image::fromPath('assets/images/header.jpg'));
            $data['envelop_image']=$message1->embed(\Swift_Image::fromPath('assets/images/envelop.png'));
            $data['footer_image']=$message1->embed(\Swift_Image::fromPath('assets/images/footer.jpg'));
            if(count($data['messageArray'])){
                $data['previous_image']=$message1->embed(\Swift_Image::fromPath('assets/images/previous.jpg'));
            }

            $rendered = $this->templating->render("mail_templates/buyer_to_seller_message_mail_to_seller.html.twig",$data);

            $message1->setBody($rendered,'text/html');
            $this->_sendMail($message1,"Student2Student : Buyer ( ".$data['buyer']." ) Sent you message",$this->parameters['host_info']['no_reply_email'], $sellerMailAddress);
        }


        if($data['buyerEntity'] instanceof User){
            if(!strcmp("On",$data['buyerEntity']->getEmailNotification())){
                $message2 = \Swift_Message::newInstance();

                for($i=0;$i<count($data['messageArray']);$i++){
                    $data['messageArray'][$i]['embedProfilePicture'] =$message2->embed(\Swift_Image::fromPath($data['messageArray'][$i]['senderProfilePicture']));
                }
                $data['book_image']=$message2->embed(\Swift_Image::fromPath(substr($data['book']->getBookImage(),1)));
                $data['header_image']=$message2->embed(\Swift_Image::fromPath('assets/images/header.jpg'));
                $data['envelop_image']=$message2->embed(\Swift_Image::fromPath('assets/images/envelop.png'));
                $data['footer_image']=$message2->embed(\Swift_Image::fromPath('assets/images/footer.jpg'));
                if(count($data['messageArray'])){
                    $data['previous_image']=$message2->embed(\Swift_Image::fromPath('assets/images/previous.jpg'));
                }

                $rendered = $this->templating->render("mail_templates/buyer_to_seller_message_mail_to_buyer.html.twig",$data);

                $message2->setBody($rendered,'text/html');
                $this->_sendMail($message2,"Student2Student : Message sent to Seller ( ".$data['seller']->getUsername()." )",$this->parameters['host_info']['no_reply_email'], $data['contact']->getBuyerEmail());
            }
        }else{
            $message2 = \Swift_Message::newInstance();

            for($i=0;$i<count($data['messageArray']);$i++){
                $data['messageArray'][$i]['embedProfilePicture'] =$message2->embed(\Swift_Image::fromPath($data['messageArray'][$i]['senderProfilePicture']));
            }
            $data['book_image']=$message2->embed(\Swift_Image::fromPath(substr($data['book']->getBookImage(),1)));
            $data['header_image']=$message2->embed(\Swift_Image::fromPath('assets/images/header.jpg'));
            $data['envelop_image']=$message2->embed(\Swift_Image::fromPath('assets/images/envelop.png'));
            $data['footer_image']=$message2->embed(\Swift_Image::fromPath('assets/images/footer.jpg'));
            if(count($data['messageArray'])){
                $data['previous_image']=$message2->embed(\Swift_Image::fromPath('assets/images/previous.jpg'));
            }

            $rendered = $this->templating->render("mail_templates/buyer_to_seller_message_mail_to_buyer.html.twig",$data);

            $message2->setBody($rendered,'text/html');
            $this->_sendMail($message2,"Student2Student : Message sent to Seller ( ".$data['seller']->getUsername()." )",$this->parameters['host_info']['no_reply_email'], $data['contact']->getBuyerEmail());
        }

        foreach($data['messageArray'] as $messageRow){
            if(file_exists($messageRow['senderProfilePicture']))unlink($messageRow['senderProfilePicture']);
        }


    }

    public function _sellerToBuyerMessageMail($data){

        $sellerMailAddress = $data['bookDeal']->getBookContactEmail()==''?$data['seller']->getEmail():$data['bookDeal']->getBookContactEmail();

        if($data['buyerEntity'] instanceof User){
            if(!strcmp("On",$data['buyerEntity']->getEmailNotification())){
                $message1 = \Swift_Message::newInstance();

                for($i=0;$i<count($data['messageArray']);$i++){
                    $data['messageArray'][$i]['embedProfilePicture'] =$message1->embed(\Swift_Image::fromPath($data['messageArray'][$i]['senderProfilePicture']));
                }
                $data['book_image']=$message1->embed(\Swift_Image::fromPath(substr($data['book']->getBookImage(),1)));
                $data['header_image']=$message1->embed(\Swift_Image::fromPath('assets/images/header.jpg'));
                $data['envelop_image']=$message1->embed(\Swift_Image::fromPath('assets/images/envelop.png'));
                $data['footer_image']=$message1->embed(\Swift_Image::fromPath('assets/images/footer.jpg'));
                if(count($data['messageArray'])){
                    $data['previous_image']=$message1->embed(\Swift_Image::fromPath('assets/images/previous.jpg'));
                }

                $rendered = $this->templating->render("mail_templates/seller_to_buyer_message_mail_to_buyer.html.twig",$data);

                $message1->setBody($rendered,'text/html');
                $this->_sendMail($message1,"Student2Student : Seller ( ".$data['seller']->getUsername()." ) Sent you message",$this->parameters['host_info']['no_reply_email'], $data['contact']->getBuyerEmail());
            }
        }else{
            $message1 = \Swift_Message::newInstance();

            for($i=0;$i<count($data['messageArray']);$i++){
                $data['messageArray'][$i]['embedProfilePicture'] =$message1->embed(\Swift_Image::fromPath($data['messageArray'][$i]['senderProfilePicture']));
            }
            $data['book_image']=$message1->embed(\Swift_Image::fromPath(substr($data['book']->getBookImage(),1)));
            $data['header_image']=$message1->embed(\Swift_Image::fromPath('assets/images/header.jpg'));
            $data['envelop_image']=$message1->embed(\Swift_Image::fromPath('assets/images/envelop.png'));
            $data['footer_image']=$message1->embed(\Swift_Image::fromPath('assets/images/footer.jpg'));
            if(count($data['messageArray'])){
                $data['previous_image']=$message1->embed(\Swift_Image::fromPath('assets/images/previous.jpg'));
            }
            $rendered = $this->templating->render("mail_templates/seller_to_buyer_message_mail_to_buyer.html.twig",$data);

            $message1->setBody($rendered,'text/html');
            $this->_sendMail($message1,"Student2Student : Seller ( ".$data['seller']->getUsername()." ) Sent you message",$this->parameters['host_info']['no_reply_email'], $data['contact']->getBuyerEmail());
        }


        if(!strcmp("On",$data['seller']->getEmailNotification())){

            $message2 = \Swift_Message::newInstance();

            for($i=0;$i<count($data['messageArray']);$i++){
                $data['messageArray'][$i]['embedProfilePicture'] =$message2->embed(\Swift_Image::fromPath($data['messageArray'][$i]['senderProfilePicture']));
            }
            $data['book_image']=$message2->embed(\Swift_Image::fromPath(substr($data['book']->getBookImage(),1)));
            $data['header_image']=$message2->embed(\Swift_Image::fromPath('assets/images/header.jpg'));
            $data['envelop_image']=$message2->embed(\Swift_Image::fromPath('assets/images/envelop.png'));
            $data['footer_image']=$message2->embed(\Swift_Image::fromPath('assets/images/footer.jpg'));
            if(count($data['messageArray'])){
                $data['previous_image']=$message2->embed(\Swift_Image::fromPath('assets/images/previous.jpg'));
            }
            $rendered = $this->templating->render("mail_templates/seller_to_buyer_message_mail_to_seller.html.twig",$data);

            $message2->setBody($rendered,'text/html');
            $this->_sendMail($message2,"Student2Student : Message sent to Buyer ( ".$data['buyer']." )",$this->parameters['host_info']['no_reply_email'], $sellerMailAddress);

        }

        foreach($data['messageArray'] as $messageRow){
            if(file_exists($messageRow['senderProfilePicture']))unlink($messageRow['senderProfilePicture']);
        }

    }

    function sendContactUsEmail($data){
        $message1 = \Swift_Message::newInstance();
        $rendered = $this->templating->render("mail_templates/contact_us_email.html.twig",$data);
        $message1->setBody($rendered,'text/html');
        $this->_sendContactUsMail($message1,"Student2Student.COM : Contact Message",$this->parameters['host_info']['no_reply_email'], 'support@student2student.com');
    }

    function sendFriendsEmail($data){
        $message1 = \Swift_Message::newInstance();

        $data['header_image']=$message1->embed(\Swift_Image::fromPath('assets/images/header_big.jpg'));
        $data['share_image']=$message1->embed(\Swift_Image::fromPath('assets/images/share.jpg'));
        $data['footer_image']=$message1->embed(\Swift_Image::fromPath('assets/images/footer.jpg'));
        $data['join_button']=$message1->embed(\Swift_Image::fromPath('assets/images/joinButton.jpg'));
        $rendered = $this->templating->render("mail_templates/share_mail_with_friends.html.twig",$data);

        $message1->setBody($rendered,'text/html');
        $this->_sendMailToMultiple($message1,"Student2Student",$this->parameters['host_info']['no_reply_email'],$data['friendEmails']);
    }

    function sendShareSellPageEmailToFriends($data){
        $message1 = \Swift_Message::newInstance();

        $data['header_image']=$message1->embed(\Swift_Image::fromPath('assets/images/header_big.jpg'));
        $data['share_image']=$message1->embed(\Swift_Image::fromPath('assets/images/share.jpg'));
        $data['footer_image']=$message1->embed(\Swift_Image::fromPath('assets/images/footer.jpg'));
        $data['buy_my_textbooks_button_image']=$message1->embed(\Swift_Image::fromPath('assets/images/buy_my_textbooks.jpg'));
        $data['books_stack_image']=$message1->embed(\Swift_Image::fromPath('assets/images/books_stack.jpg'));
        $data['shareUrl'] = $this->parameters['host_info']['host_url']."/".$data['username'];

        $rendered = $this->templating->render("mail_templates/share_sell_page_mail_with_friends.html.twig",$data);

        $message1->setBody($rendered,'text/html');
        $this->_sendMailToMultiple($message1,$data['username']."'s Sell Page | Student2Student",$this->parameters['host_info']['no_reply_email'],$data['friendEmails']);
    }




    public function _sendMail($message,$subject,$from,$to){
        $transport = \Swift_SmtpTransport::newInstance($this->parameters['host_info']['mail_host'], $this->parameters['host_info']['mail_port'],$this->parameters['host_info']['mail_security'])
            ->setUsername($this->parameters['host_info']['no_reply_email'])
            ->setPassword($this->parameters['host_info']['no_reply_password']);

        $mailer = \Swift_Mailer::newInstance($transport);

        $message
            ->setSubject($subject)
            ->setFrom($this->parameters['host_info']['no_reply_email'])
            ->setTo($to);
        $mailer->send($message);
    }
    public function _sendContactUsMail($message,$subject,$from,$to){
        $transport = \Swift_SmtpTransport::newInstance($this->parameters['host_info']['mail_host'], $this->parameters['host_info']['mail_port'],$this->parameters['host_info']['mail_security'])
            ->setUsername($this->parameters['host_info']['no_reply_email'])
            ->setPassword($this->parameters['host_info']['no_reply_password']);

        $mailer = \Swift_Mailer::newInstance($transport);

        $message
            ->setSubject($subject)
            ->setFrom($this->parameters['host_info']['no_reply_email'])
            ->setTo($to);
        $mailer->send($message);
    }
    public function _sendMailToMultiple($message,$subject,$from,$toes){
        $transport = \Swift_SmtpTransport::newInstance($this->parameters['host_info']['mail_host'], $this->parameters['host_info']['mail_port'],$this->parameters['host_info']['mail_security'])
            ->setUsername($this->parameters['host_info']['no_reply_email'])
            ->setPassword($this->parameters['host_info']['no_reply_password']);

        $mailer = \Swift_Mailer::newInstance($transport);

        $message
            ->setSubject($subject)
            ->setFrom($this->parameters['host_info']['no_reply_email']);

        foreach($toes as $to){
            $message->addBcc($to['email']);
        }
        $message->setTo($this->parameters['host_info']['no_reply_email']);

        $mailer->send($message);
    }


}
