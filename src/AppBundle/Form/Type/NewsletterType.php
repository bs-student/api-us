<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\DateTime;
class NewsletterType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email','email',array(
                'constraints' => array(
                    new NotBlank(),
                    new Email(),
                )
            ))
            ->add('lastUpdateDateTime','datetime',array(
                'widget' => 'single_text',
                'constraints' => array(
                    new DateTime(),
                    new NotBlank()
                )
            ))
            ->add('activationStatus','text',array(
                'constraints' => array(
                    new NotBlank()
                )
            ))

        ;
    }
    

    /**
     * @return string
     */
    public function getName()
    {
        return 'appbundle_newsletter';
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Newsletter',
            'csrf_protection' => false,
            'allow_extra_fields' => true,
        ));
    }


}
