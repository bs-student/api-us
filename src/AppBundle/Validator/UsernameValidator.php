<?php
/**
 * Created by PhpStorm.
 * User: Sujit
 * Date: 2/4/16
 * Time: 5:16 PM
 */

namespace AppBundle\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UsernameValidator extends ConstraintValidator
{


    protected $em;


    public function __construct(\Doctrine\ORM\EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Checks if the passed value is valid.
     *
     * @param mixed $value The value that should be validated
     * @param Constraint $constraint The constraint for the validation
     *
     * @return bool
     * @api
     */
    public function validate($value, Constraint $constraint)
    {

        $userRepo = $this->em->getRepository('AppBundle:User');

        var_dump($userRepo->checkIfUsernameExistByUsername($value, $constraint->username));

        if(!$userRepo->checkIfUsernameExistByUsername($value, $constraint->username)){

            $this->setMessage($constraint->message, array('' => $value));

            return false;
        }else{
            return true;
        }
    }
}