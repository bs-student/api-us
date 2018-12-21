<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Validator\Constraints\DateTime;
class WishListType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {


        $builder
            ->add('book', 'entity',array(
                'class' => "AppBundle:Book",
                'constraints' => array(
                    new NotBlank(),
                )))
            ->add('user', 'entity',array(
                'class' => "AppBundle:User",
                'constraints' => array(
                    new NotBlank(),
                )));



    }


    /**
     * @return string
     */
    public function getName()
    {
        return 'appbundle_wishlist_add';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\WishList',
            'csrf_protection' => false,
            'allow_extra_fields' => true,
        ));
    }
}
