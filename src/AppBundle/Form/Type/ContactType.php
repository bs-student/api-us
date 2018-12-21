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
class ContactType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {


        $builder
            ->add('buyerNickName', 'text')

            ->add('buyerEmail', 'text', array(
                'constraints' => array(
                    new NotBlank(),
                    new Email()
                ),))

            ->add('buyerHomePhone', 'text')

            ->add('buyerCellPhone', 'text')
            ->add('soldToThatBuyer', 'text')

            ->add('bookDeal', 'entity',array(
                'class' => "AppBundle:BookDeal",
                'constraints' => array(
                    new NotBlank(),
                )))

            ->add('buyer', 'entity',array(
                'class' => "AppBundle:User"
            ))
            ->add('messages', 'collection', array(
                'type'         => new MessageType(),
                'allow_add'    => true,
//                'allow_delete'    => true,
                'by_reference' =>false

            ))

            ->add('contactDateTime','datetime',array(
                'widget' => 'single_text',
                'constraints' => array(
                    new DateTime(),
                    new NotBlank()
                )
            ))
            ->add('soldToThatBuyer','text')
            ->add('contactCondition','text');



    }




    /**
     * @return string
     */
    public function getName()
    {
        return 'appbundle_contact_add';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Contact',
            'csrf_protection' => false,
            'allow_extra_fields' => true,
        ));
    }
}
