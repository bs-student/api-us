<?php
namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use AppBundle\Repository\ReferralRepository;
use AppBundle\Repository\CampusRepository;
use Symfony\Component\Validator\Constraints\NotBlank;


use Symfony\Component\OptionsResolver\OptionsResolver;

class CampusType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {


        $builder->add('campusName','text',array(
            'constraints' => array(
                new NotBlank(),

            ),
        ));


        $builder->add('state','entity',array(
            'class' => "AppBundle:State",
            'constraints' => array(
                new NotBlank(),

            ),
        ));

        $builder->add('campusStatus','text',array(
            'constraints' => array(
                new NotBlank(),
            ),
        ));

    }


    public function getName()
    {
        return 'app_campus_update';
    }


    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Campus',
            'csrf_protection' => false,
            'allow_extra_fields' => true,

        ));
    }

}