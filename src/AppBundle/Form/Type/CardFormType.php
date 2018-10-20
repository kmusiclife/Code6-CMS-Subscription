<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

// Form Type
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TelType;

// Injection Classes
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\ORM\EntityManagerInterface;

class CardFormType extends AbstractType
{

    protected $userManager;
    protected $serviceContainer;
    protected $entityManager;

    public function __construct(ContainerInterface $serviceContainer, UserManagerInterface $userManager, EntityManagerInterface $entityManager)
    {
        $this->serviceContainer = $serviceContainer;
        $this->userManager = $userManager;
        $this->entityManager = $entityManager;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
	
		$this->serviceContainer->get('app.stripe_helper')->setApiKey();
		
		$builder->remove('email');
		$builder->remove('username');
		$builder->remove('plainPassword');
	    $builder->add('stripe_token_id', HiddenType::class, ['error_bubbling' => false]);
	    
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
