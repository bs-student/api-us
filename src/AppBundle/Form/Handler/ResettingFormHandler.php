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

use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use FOS\UserBundle\Form\Model\ChangePassword;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use FOS\UserBundle\Form\Handler\ResettingFormHandler as BaseHandler;

class ResettingFormHandler extends BaseHandler
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

        $submittedData['new']=array(
            'first'=>$submittedData['password'],
            'second'=>$submittedData['passwordConfirm']
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
        $user->setConfirmationToken(null);
        $user->setPasswordRequestedAt(null);
        $user->setEnabled(true);
        $this->userManager->updateUser($user);
    }

    function getSubmittedData(){
        return json_decode($this->request->getContent(),true);
    }
}
