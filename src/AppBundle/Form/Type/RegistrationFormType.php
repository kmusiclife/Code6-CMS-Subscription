<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

// Injection Classes
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class RegistrationFormType extends AbstractType
{

    protected $userManager;
    protected $serviceContainer;
	protected $entityManager;
	protected $requestStack;

    public function __construct(
    	ContainerInterface $serviceContainer, 
    	UserManagerInterface $userManager, 
		EntityManagerInterface $entityManager,
		RequestStack $requestStack
    ){
        $this->serviceContainer = $serviceContainer;
        $this->userManager = $userManager;
		$this->entityManager = $entityManager;
		$this->requestStack = $requestStack;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
		
		$this->serviceContainer->get('subscription.stripe_helper')->setApiKey();
		
	    $stripe_plans = \Stripe\Plan::all();
		
		$plans = array();
		
		foreach( $stripe_plans->data as $plan ){
			
			if($plan->currency == 'jpy') setlocale( LC_MONETARY, 'ja_JP.UTF-8' );
			if($plan->currency == 'usd') setlocale( LC_MONETARY, 'en_US.UTF-8' );

			$label = $plan->name . ' '. money_format('%.0n', $plan->amount) . ' / ' . $plan->interval;
			$plans[$label] = $plan->id;
			
		}
		$plan_id_choices = array(
			'choices'  => $plans
		);
		
	    $builder->add('fname');
	    $builder->add('lname');
	    $builder->add('zip');
	    $builder->add('address');
	    $builder->add('tel', TelType::class);
	    $builder->add('facebook_url', HiddenType::class);
	    $builder->add('stripe_plan_id', ChoiceType::class, $plan_id_choices);
		$builder->add('stripe_token_id', HiddenType::class, ['error_bubbling' => false]);
		
		/*
		$request = $this->requestStack->getCurrentRequest();
		$code = $request->request->get('code');
		$invitation = $this->entityManager->getRepository('AppBundle:Invitation')->findOneBy(array('code' => $code));
		*/

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
