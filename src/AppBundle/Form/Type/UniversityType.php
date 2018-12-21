<?php
namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use AppBundle\Repository\ReferralRepository;
use AppBundle\Repository\CampusRepository;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Validator\Constraints\NotBlank;
use AppBundle\Form\Type\CampusType;

use Symfony\Component\OptionsResolver\OptionsResolver;

class UniversityType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {


        $builder->add('universityName','text',array(
            'constraints' => array(
                new NotBlank(),
            )
        ));

        $builder->add('universityUrl','text');

        $builder->add('referral', 'entity', array(
            'class' => "AppBundle:Referral",
            'property' => 'referralName',
            'constraints' => array(
                new NotBlank(),

            )

        ));
        $builder->add('universityStatus','text',array(
            'constraints' => array(
                new NotBlank(),
            )
        ));
        $builder->add('campuses', 'collection', array(
            'type'         => new CampusType(),
            'allow_add'    => true,
            'by_reference' =>false
        ));

        $builder->add('adminApproved','text',array(
            'constraints' => array(
                new NotBlank(),
            )
        ));
        $builder->add('creationDateTime','datetime',array(
            'widget' => 'single_text',
            'constraints' => array(
                new DateTime(),
                new NotBlank()
            )
        ));

    }


    public function getName()
    {
        return 'app_university_update';
    }




    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\University',
            'csrf_protection' => false,
            'allow_extra_fields' => true,
        ));
    }

}