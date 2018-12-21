<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\DateTime;
class LogType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('logDescription','text',array(
                'constraints' => array(
                    new NotBlank(),

                )
            ))
            ->add('logDateTime','datetime',array(
                'widget' => 'single_text',
                'constraints' => array(
                    new DateTime(),
                    new NotBlank()
                )
            ))
            ->add('logType','text',array(
                'constraints' => array(
                    new NotBlank()
                )
            ))
            ->add('userIpAddress','text',array(
                'constraints' => array(
                    new NotBlank()
                )
            ))
            ->add('logUserType','text',array(
                'constraints' => array(
                    new NotBlank()
                )
            ))
            ->add('user','entity',array(
                'class' => "AppBundle:User"/*,
                'constraints' => array(
                    new NotBlank(),

                )*/
            ))
        ;
    }
    

    /**
     * @return string
     */
    public function getName()
    {
        return 'appbundle_log';
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Log',
            'csrf_protection' => false,
            'allow_extra_fields' => true,

        ));
    }


}
