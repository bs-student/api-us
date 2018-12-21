<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\BookDealImage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
class BookDealType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder

            ->add('book','entity',array(
                'class' => "AppBundle:Book",
                'constraints' => array(
                    new NotBlank(),

                )))

            ->add('bookPriceSell', 'text', array(
                'constraints' => array(
                    new NotBlank(),

                ),))

            ->add('bookCondition', 'text', array(
                'constraints' => array(
                    new NotBlank(),

                ),))
            ->add('bookIsHighlighted', 'text', array(
                'constraints' => array(
                    new NotBlank(),

                ),))
            ->add('bookHasNotes', 'text', array(
                'constraints' => array(
                    new NotBlank(),

                ),))
            ->add('bookComment', 'text')
            ->add('bookContactMethod', 'text', array(
                'constraints' => array(
                    new NotBlank(),

                ),))
            ->add('bookContactHomeNumber','text')
            ->add('bookContactCellNumber','text')
            ->add('bookContactEmail','text')
            ->add('bookIsAvailablePublic', 'text', array(
                'constraints' => array(
                    new NotBlank(),

                ),))
            ->add('bookPaymentMethodCashOnExchange')
            ->add('bookPaymentMethodCheque')
            ->add('bookAvailableDate', 'date', array(
                'widget' => 'single_text',
                'constraints' => array(
                    new NotBlank(),
                    new Date()
                ),))
            ->add('buyer','entity',array(
                'class' => "AppBundle:User",
                ))
            ->add('seller','entity',array(
                'class' => "AppBundle:User",
                'constraints' => array(
                    new NotBlank(),

                )))
            ->add('bookSellingStatus', 'text', array(
                'constraints' => array(
                    new NotBlank(),

                ),))
            ->add('bookStatus', 'text', array(
                'constraints' => array(
                    new NotBlank(),

                ),))
            ->add('bookViewCount', 'integer', array(
                'constraints' => array(
                    new NotBlank(),

                ),))

            ->add('bookDealImages', 'collection', array(
                'type'         => new BookDealImageType(),
                'allow_add'    => true,
//                'allow_delete'    => true,
                'by_reference' =>false

            ))

            ->add('bookSubmittedDateTime','datetime',array(
                'widget' => 'single_text',
                'constraints' => array(
                    new DateTime(),
                    new NotBlank()
            )
    ));

    }



    /**
     * @return string
     */
    public function getName()
    {
        return 'appbundle_book_deal';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\BookDeal',
            'csrf_protection' => false,
            'allow_extra_fields' => true,


        ));
    }
}
