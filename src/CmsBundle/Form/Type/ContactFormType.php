<?php

namespace CmsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\HiddenType;

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
        $builder->add('recaptcha');
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {

    }

    public function getBlockPrefix()
    {
        return 'sitebundle_contact';
    }


}
