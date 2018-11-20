<?php

namespace CmsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class ContactEditFormType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title', HiddenType::class);
        $builder->add('email', HiddenType::class);
        $builder->add('name', HiddenType::class);
        $builder->add('tel', HiddenType::class);
        $builder->add('zip', HiddenType::class);
        $builder->add('address', HiddenType::class);
        $builder->add('body', HiddenType::class);

    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'SiteBundle\Entity\Contact'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sitebundle_contact';
    }


}
