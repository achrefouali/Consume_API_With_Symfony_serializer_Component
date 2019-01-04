<?php
/**
 * Created by PhpStorm.
 * User: acf
 * Date: 03/01/2019
 * Time: 16:29
 */

namespace AtsBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
class FilterType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {

        $builder
            ->add('title', TextType::class, array('translation_domain' => 'AtsBundle',
                'label' => 'Title',
                'required' => false,
                'attr' => array('class' => 'form-control','placeholder'=>'Choisir un titre')
            ))
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(

        ));
    }

    /**
     * @return string
     */
    public function getBlockPrefix() {
        return 'application_entry_filter_type';
    }


}