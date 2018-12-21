<?php
namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use AppBundle\Repository\ReferralRepository;
use AppBundle\Repository\CampusRepository;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;


use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {


        $builder->add('fullName','text',array(
            'constraints' => array(
                new NotBlank(),

            )
        ));

        $builder->add('username','text',array(
            'constraints' => array(
                new NotBlank(),

            )
        ));


        $builder->add('email','email',array(
            'constraints' => array(
                new NotBlank(),
                new Email(),
            )
        ));




        $builder->add('referral', 'entity', array(
            'class' => "AppBundle:Referral",
            'property' => 'referralName',
            'constraints' => array(
                new NotBlank(),

            )

        ));

        $builder->add('campus', 'entity', array(
            'class' => "AppBundle:Campus",
            'property' => 'campusName',
            'constraints' => array(
                new NotBlank(),

            )
        ));

        $builder->add('wishLists', 'collection', array(
            'type'         => new WishListType(),
            'allow_add'    => true,
            'by_reference' =>false
        ));

        $builder->add('adminApproved','text',array(
            'constraints' => array(
                new NotBlank(),

            )
        ));
        $builder->add('emailVerified','text',array(
            'constraints' => array(
                new NotBlank(),

            )
        ));
        $builder->add('adminVerified','text',array(
            'constraints' => array(
                new NotBlank(),

            )
        ));

    }


    public function getName()
    {
        return 'app_created_user_update';
    }




    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\User',
            'csrf_protection' => false,
            'allow_extra_fields' => true,
        ));
    }

}