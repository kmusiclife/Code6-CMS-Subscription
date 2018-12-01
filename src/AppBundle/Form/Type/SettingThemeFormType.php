<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class SettingThemeFormType extends AbstractType
{

    protected $serviceContainer;

    public function __construct(
    	ContainerInterface $serviceContainer
    ){
        $this->serviceContainer = $serviceContainer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $_theme_names = $this->serviceContainer->get('app.app_helper')->getThemeNames();
        $_theme_name_choice = array();
        foreach($_theme_names as $_theme_name){
            $_theme_name_choice[$_theme_name] = $_theme_name;
        }
        $builder->add('value', ChoiceType::class, array(
            'choices'  => $_theme_name_choice,
        ));
        $builder->add('slug', HiddenType::class, array('data' => 'parameter_theme_name'));

    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Setting'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_setting';
    }


}
