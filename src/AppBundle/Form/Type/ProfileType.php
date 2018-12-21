<?php
namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use AppBundle\Repository\ReferralRepository;
use AppBundle\Repository\CampusRepository;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;


use Symfony\Component\OptionsResolver\OptionsResolver;

class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add('campus', 'entity', array(
            'class' => "AppBundle:Campus",
            'property' => 'campusName',
            'constraints' => array(
                new NotBlank(),
            )
        ));

        $builder->add('standardHomePhone','text');
        $builder->add('standardCellPhone','text');
        $builder->add('standardEmail','text');
        $builder->add('profilePicture','text');
        $builder->add('fullName','text',array(
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