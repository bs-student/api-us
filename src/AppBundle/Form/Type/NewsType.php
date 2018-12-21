<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\BookDealImage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
class NewsType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder



            ->add('newsTitle', 'text', array(
                'constraints' => array(
                    new NotBlank(),

                ),))

            ->add('newsDescription', 'text', array(
                'constraints' => array(
                    new NotBlank(),

                ),))


            ->add('newsDateTime', 'datetime', array(
                'widget' => 'single_text',
                'constraints' => array(
                    new NotBlank(),
                    new DateTime()
                ),))



            ->add('newsStatus', 'text', array(
                'constraints' => array(
                    new NotBlank(),

                ),))
            ->add('newsImages', 'collection', array(
                'type'         => new NewsImageType(),
                'allow_add'    => true,
//                'allow_delete'    => true,
                'by_reference' =>false

            ))
            ->add('newsVideoEmbedCode', 'text', array(
                'constraints' => array(
                    new NotBlank(),

                ),))

            ->add('newsType', 'text', array(
                'constraints' => array(
                    new NotBlank(),

                ),))
        ;

    }



    /**
     * @return string
     */
    public function getName()
    {
        return 'appbundle_news';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\News',
            'csrf_protection' => false,
            'allow_extra_fields' => true,


        ));
    }
}
