<?php

namespace CmsBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleEditFormType extends ArticleFormType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
	    parent::buildForm($builder, $options);
        $builder->add('eyecatch', ImageSimpleFormType::class, array());
        
    }
    public function getBlockPrefix()
    {
        return 'cmsbundle_article_edit';
    }


}
