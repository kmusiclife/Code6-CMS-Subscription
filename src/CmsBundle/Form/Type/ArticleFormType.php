<?php

namespace CmsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\File;

use CmsBundle\Form\Type\ImageSimpleFormType;
use CmsBundle\Entity\Image;
use CmsBundle\Entity\Article;

use Symfony\Component\DependencyInjection\ContainerInterface;

class ArticleFormType extends AbstractType
{

    protected $serviceContainer;

    public function __construct(
    	ContainerInterface $serviceContainer
    ){
        $this->serviceContainer = $serviceContainer;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title');
        $builder->add('slug');
        $builder->add('is_published', CheckboxType::class, array(
			'required' => false,
        ));
        if( $this->serviceContainer->getParameter('members_mode') ){
	        $builder->add('is_member', CheckboxType::class, array(
				'required' => false,
	        ));
	    }
        $builder->add('body');

        $builder->add('images', CollectionType::class, array(
            'entry_type' => ImageSimpleFormType::class,
            'entry_options' => array('required' => false),
        ));
        $builder->add('publishedat', DateTimeType::class, array(
        	'widget' => 'single_text',
        	'html5' => false,
        ));
        $builder->add('seo', SeoRequireFormType::class, array());

    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Article::class
        ));
    }

    public function getBlockPrefix()
    {
        return 'cmsbundle_article';
    }


}
