<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Form\Handler;

use FOS\UserBundle\Form\Model\ChangePassword;
use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use FOS\UserBundle\Form\Handler\ChangePasswordFormHandler as BaseHandler;

class ChangePasswordFormHandler extends BaseHandler
{


    /**
     * @return string
     */
    public function getNewPassword()
    {
        return $this->form->getData()->new;
    }

    public function process(UserInterface $user)
    {
        $this->form->setData(new ChangePassword());


        $submittedData = json_decode($this->request->getContent(),true);

        $submittedData['current_password'] = array_key_exists('oldPassword',$submittedData)? $submittedData['oldPassword']:null;
        $newPassword = array_key_exists('newPassword',$submittedData)? $submittedData['newPassword']:null;
        $newPasswordConfirm = array_key_exists('newPasswordConfirm',$submittedData)? $submittedData['newPasswordConfirm']:null;

        $submittedData['new']=array(
            'first'=>$newPassword,
            'second'=>$newPasswordConfirm
        );

        if ('POST' === $this->request->getMethod()) {
            $this->form->bind($submittedData);

            if ($this->form->isValid()) {
                $this->onSuccess($user);

                return true;
            }
        }

        return false;
    }

    protected function onSuccess(UserInterface $user)
    {
        $user->setPlainPassword($this->getNewPassword());
        $this->userManager->updateUser($user);
    }
}
