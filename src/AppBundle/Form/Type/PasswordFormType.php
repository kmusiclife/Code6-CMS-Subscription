<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PasswordFormType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
	    $builder->remove('email');
	    $builder->remove('username');
    }

    public function getName()
    {
        return $this->getBlockPrefix();
    }
    public function getParent()
    {
        return 'FOS\UserBundle\Form\Type\ProfileFormType';
    }

}
