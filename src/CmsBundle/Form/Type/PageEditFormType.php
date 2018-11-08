<?php

namespace CmsBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\DependencyInjection\ContainerInterface;

class PageEditFormType extends PageFormType
{
	
    protected $serviceContainer;

    public function __construct(
    	ContainerInterface $serviceContainer
    ){
        $this->serviceContainer = $serviceContainer;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
	    parent::buildForm($builder, $options);
        $builder->add('seo', SeoFormType::class, array());
    }
    public function getBlockPrefix()
    {
        return 'cmsbundle_article_edit';
    }

}
