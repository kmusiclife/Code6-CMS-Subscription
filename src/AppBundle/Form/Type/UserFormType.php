<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

// Form Type
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Blank;

use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TelType;

use Symfony\Component\DependencyInjection\ContainerInterface;

class UserFormType extends AbstractType
{
    protected $serviceContainer;

    public function __construct(
    	ContainerInterface $serviceContainer
    ){
        $this->serviceContainer = $serviceContainer;
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
	    $builder->add('username');
	    $builder->add('email');

        $_theme_names = $this->serviceContainer->get('app.app_helper')->getThemeNames();
        $_theme_name_choice = array();
        foreach($_theme_names as $_theme_name){
            $_theme_name_choice[$_theme_name] = $_theme_name;
        }
        $builder->add('theme', ChoiceType::class, array(
            'required'   => false,
            'empty_data' => null,
            'choices'  => $_theme_name_choice,
        ));

        $builder->remove('current_password');
        $builder->add('plainPassword', RepeatedType::class, array(
            'type' => PasswordType::class,
            'options' => array(
                'translation_domain' => 'FOSUserBundle',
                'attr' => array(
                    'autocomplete' => 'new-password',
                ),
            ),
            'first_options' => array('label' => 'form.new_password'),
            'second_options' => array('label' => 'form.new_password_confirmation'),
            'invalid_message' => 'fos_user.password.mismatch',
        ));
	    
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
