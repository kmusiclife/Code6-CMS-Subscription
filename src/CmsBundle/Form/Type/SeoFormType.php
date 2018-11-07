<?php

namespace CmsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

use Symfony\Component\Validator\Constraints as Assert;

class SeoFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('description', null, array('required' => false));
        $builder->add('keywords', TextType::class, array('required' => false));
        $builder->add('image', ImageSimpleFormType::class, array());
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CmsBundle\Entity\Seo'
        ));
    }

    public function getBlockPrefix()
    {
        return 'cmsbundle_seo';
    }

}
