<?php
namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use AppBundle\Repository\ReferralRepository;
use AppBundle\Repository\CampusRepository;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\DateTime;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('fullName','text',array(
            'constraints' => array(
                new NotBlank(),

            )
        ));

        $builder->add('googleId','text',array(
            'constraints' => array(
                new NotBlank(),

            )
        ));

        $builder->add('facebookId','text',array(
            'constraints' => array(
                new NotBlank(),

            )
        ));

        $builder->add('registrationStatus','text',array(
            'constraints' => array(
                new NotBlank(),

            )
        ));

        $builder->add('googleEmail','text',array(
            'constraints' => array(
                new NotBlank(),

            )
        ));

        $builder->add('googleToken','text',array(
            'constraints' => array(
                new NotBlank(),

            )
        ));

        $builder->add('facebookEmail','text',array(
            'constraints' => array(
                new NotBlank(),

            )
        ));

        $builder->add('facebookToken','text',array(
            'constraints' => array(
                new NotBlank(),

            )
        ));


        $builder->add('referral', 'entity', array(
            'constraints' => array(
                new NotBlank(),

            ),
            'class' => "AppBundle:Referral",
            'empty_value' => 'Choose an option',
            'query_builder' => function(ReferralRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->orderBy('u.id', 'ASC');
                },
            'property' => 'referralName',


        ));

        $builder->add('campus', 'entity', array(
            'constraints' => array(
                new NotBlank(),

            ),
            'class' => "AppBundle:Campus",
            'empty_value' => 'Choose University Campus',
            'query_builder' => function(CampusRepository $er) {
                    return $er->getCampus();
                },
            'property' => 'campusName',


        ));
//        $builder->add('adminApproved','text');
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
        $builder->add('profilePicture','text');
        $builder->add('emailNotification','text');
        $builder->add('registrationDateTime','datetime',array(
            'widget' => 'single_text',
            'constraints' => array(
                new DateTime(),
                new NotBlank()
            )
        ));
    }

    public function getParent()
    {
        return 'fos_user_registration';
    }

    public function getName()
    {
        return 'app_user_registration';
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