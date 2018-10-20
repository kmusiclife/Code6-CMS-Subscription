<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

// Injection Classes
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\ORM\EntityManagerInterface;

class RegistrationFormType extends AbstractType
{

    protected $userManager;
    protected $serviceContainer;
    protected $entityManager;

    public function __construct(
    	ContainerInterface $serviceContainer, 
    	UserManagerInterface $userManager, 
    	EntityManagerInterface $entityManager
    ){
        $this->serviceContainer = $serviceContainer;
        $this->userManager = $userManager;
        $this->entityManager = $entityManager;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    }
    public function getName()
    {
        return $this->getBlockPrefix();
    }
    public function getParent()
    {
        return 'FOS\UserBundle\Form\Type\RegistrationFormType';
    }

}
