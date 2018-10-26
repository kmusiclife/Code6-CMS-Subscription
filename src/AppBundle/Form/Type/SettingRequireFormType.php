<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints as Assert;

use AppBundle\Form\Type\SettingFormType as BaseFormType;

class SettingRequireFormType extends BaseFormType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('slug', TextType::class, array(
	        'constraints' => array(
		        new Assert\NotBlank()
	        )
        ));
        $builder->add('value', TextAreaType::class, array(
	        'constraints' => array(
		        new Assert\NotBlank()
	        )
        ));
        
    }

}
