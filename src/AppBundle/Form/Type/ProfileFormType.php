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

// Injection Classes
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ProfileFormType extends AbstractType
{

    protected $userManager;
    protected $serviceContainer;
    protected $entityManager;
    protected $tokenStorage;
    protected $user;
    
    public function __construct(
    	ContainerInterface $serviceContainer, 
    	UserManagerInterface $userManager, 
    	EntityManagerInterface $entityManager,
		TokenStorageInterface $tokenStorage
    )
    {
        $this->serviceContainer = $serviceContainer;
        $this->userManager = $userManager;
        $this->entityManager = $entityManager;
		$this->tokenStorage = $tokenStorage;
		
		$this->user = $this->tokenStorage->getToken()->getUser();
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
	    
	    // $this->serviceContainer->get('security.authorization_checker')->isGranted('ROLE_ADMIN')
	    $builder->add('username');
	    $builder->add('email');
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
