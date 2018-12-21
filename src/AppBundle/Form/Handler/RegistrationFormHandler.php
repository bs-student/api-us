<?php
namespace AppBundle\Form\Handler;

use FOS\UserBundle\Form\Handler\RegistrationFormHandler as BaseHandler;
use FOS\UserBundle\Model\UserInterface;

class RegistrationFormHandler extends BaseHandler
{
    protected function onSuccess(UserInterface $user, $confirmation)
    {
        // Note: if you plan on modifying the user then do it before calling the
        // parent method as the parent method will flush the changes

        $user->addRole('ROLE_NORMAL_USER');
        $user->setRegistrationStatus('complete');
        $user->setSalt($this->random_key_generator(20));


        parent::onSuccess($user, $confirmation);


        // otherwise add your functionality here
    }
    public function process($confirmation = false)
    {
        $user = $this->createUser();
        $this->form->setData($user);

        $submittedData = json_decode($this->request->getContent(),true);
        $submittedData['profilePicture']="/userImages/default_profile_picture.jpg";
        $submittedData['emailNotification']="On";
        $submittedData['registrationDateTime']=gmdate('Y-m-d H:i:s');
        $submittedData['plainPassword']=array(
            'first'=>$submittedData['new_password'],
            'second'=>$submittedData['confirm_password']
        );
        $submittedData['adminApproved']='Yes';
        $submittedData['emailVerified']='No';
        $submittedData['adminVerified']='No';

        if ('POST' === $this->request->getMethod()) {

            $this->form->bind($submittedData);

            if ($this->form->isValid()) {
                $this->onSuccess($user, $confirmation);

                return true;
            }
        }

        return false;
    }


    function random_key_generator($character_length){

        $possible = '23456789bcdfghjkmnpqrstvwxyz_-=()+BCDFGHJKMNPQRSTVWXYZ';
        $salt_code = '';
        $i = 0;
        while ($i < $character_length) {
            $salt_code .= substr($possible, mt_rand(0, strlen($possible)-1), 1);
            $i++;
        }
        return $salt_code;
    }

    function getSubmittedData(){
        return json_decode($this->request->getContent(),true);
    }
}