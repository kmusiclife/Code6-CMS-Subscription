<?php

namespace CmsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

use Symfony\Component\Validator\Constraints as Assert;
use CmsBundle\Entity\Image;

class ImageSimpleFormType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('file', FileType::class, array(
	        'data_class' => null,
	        'label' => false,
	        'required' => false,
	        'attr' => array(
		        'accept' => 'image/*'
	        ),
	        'constraints' => [
	        ]
        ));

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Image::class
        ));
    }

    public function getBlockPrefix()
    {
        return 'cmsbundle_image_simple';
    }


}
