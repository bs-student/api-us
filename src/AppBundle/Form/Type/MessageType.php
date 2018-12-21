<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\DateTime;
class MessageType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('messageBody','text',array(
                'constraints' => array(
                    new NotBlank(),

                )
            ))
            ->add('messageDateTime','datetime',array(
                'widget' => 'single_text',
                'constraints' => array(
                    new DateTime(),
                    new NotBlank()
                )
            ))
            ->add('messageType','text',array(
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
        return 'appbundle_message';
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Message',
            'csrf_protection' => false,
            'allow_extra_fields' => true,

        ));
    }


}
