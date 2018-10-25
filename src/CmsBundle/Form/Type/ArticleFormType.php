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


class ArticleFormType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title');
        $builder->add('is_published', CheckboxType::class, array(
			'required' => false,
        ));
        $builder->add('body');

        $builder->add('images', CollectionType::class, array(
            'entry_type' => ImageSimpleFormType::class,
            'entry_options' => array('required' => false),
        ));
        $builder->add('publishedat', DateTimeType::class, array(
        	'widget' => 'single_text',
        	'html5' => false,
        ));
        $builder->add('eyecatch', ImageSimpleRequireFormType::class, array());
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
