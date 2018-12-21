<?php
/**
 * Created by PhpStorm.
 * User: Sujit
 * Date: 2/4/16
 * Time: 5:13 PM
 */

namespace AppBundle\Validator\Constraints;


use Symfony\Component\Validator\Constraint;
use AppBundle\Entity\User;

class UsernameConstraints extends Constraint {

    public $message = 'This username already exist';
    public $username = null;

    public function __construct(User $user)
    {
        $this->username = $user->getUsername();
    }

    public function validatedBy()
    {
        return 'app.validator.username_exist';
    }
} 