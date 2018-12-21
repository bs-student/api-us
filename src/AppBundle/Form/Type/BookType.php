<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
class BookType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('bookTitle', 'text', array(
                'constraints' => array(
                    new NotBlank(),

                ),))
            ->add('bookDirectorAuthorArtist', 'text', array(
                'constraints' => array(
                    new NotBlank(),

                ),))
            ->add('bookEdition', 'text')
            ->add('bookIsbn10', 'text')
            ->add('bookIsbn13', 'text')

            ->add('bookPublisher', 'text')
            ->add('bookPublishDate','date', array(
                'widget' => 'single_text',
                'constraints' => array(
                    new Date(),

                ),))
            ->add('bookBinding', 'text', array(
                'constraints' => array(
                    new NotBlank(),

                ),))
            ->add('bookPage','text')
            ->add('bookLanguage','text')
            ->add('bookDescription','text')
            ->add('bookAmazonPrice','text')

            ->add('bookImage', 'text', array(
                'constraints' => array(
                    new NotBlank(),

                )
            ));


    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appbundle_book';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Book',
            'csrf_protection' => false,
            'allow_extra_fields' => true,

        ));
    }
}
