<?php

namespace SiteBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactFormType extends AbstractType
{
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title');
        $builder->add('email');
        $builder->add('name');
        $builder->add('tel');
        $builder->add('zip');
        $builder->add('address');
        $builder->add('body');
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'SiteBundle\Entity\Contact'
        ));
    }

    public function getBlockPrefix()
    {
        return 'sitebundle_contact';
    }


}
