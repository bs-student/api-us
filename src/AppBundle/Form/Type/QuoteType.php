<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
class QuoteType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('quoteProvider', 'text', array(
                'constraints' => array(
                    new NotBlank(),

                ),))
            ->add('quoteDescription', 'text', array(
                'constraints' => array(
                    new NotBlank(),

                ),))
            ->add('quoteStatus', 'text',array(
                'constraints' => array(
                    new NotBlank(),

                )
            ))
            ->add('quoteType', 'text')
            ->add('quoteImage', 'text');

    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appbundle_quote';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Quote',
            'csrf_protection' => false,
            'allow_extra_fields' => true,

        ));
    }
}
