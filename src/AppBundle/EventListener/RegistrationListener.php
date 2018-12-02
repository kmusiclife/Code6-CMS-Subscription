<?php

namespace AppBundle\EventListener;

use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;

// source
use Symfony\Component\Form\FormError;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

// Injection Classes
use FOS\UserBundle\Util\TokenGeneratorInterface;
use FOS\UserBundle\Mailer\MailerInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class RegistrationListener implements EventSubscriberInterface
{

	protected $userManager;
	protected $serviceContainer;
	protected $entityManager;
	protected $router;
	
	protected $form;
	protected $user;

	public function __construct(
		MailerInterface $mailer,
		TokenGeneratorInterface $tokenGenerator, 
		ContainerInterface $serviceContainer, 
		UserManagerInterface $userManager, 
		EntityManagerInterface $entityManager, 
		UrlGeneratorInterface $router
	){
		$this->mailer = $mailer;
		$this->tokenGenerator = $tokenGenerator;
		$this->serviceContainer = $serviceContainer;
		$this->userManager = $userManager;
		$this->EntityManager = $entityManager;
		$this->router = $router;
		
		$this->form = null;
		$this->user = null;
	}

	public static function getSubscribedEvents()
	{
		return array(
			FOSUserEvents::REGISTRATION_INITIALIZE => 'onRegistrationInitialize',
			FOSUserEvents::REGISTRATION_SUCCESS => 'onRegistrationSuccess',
			FOSUserEvents::REGISTRATION_COMPLETED => 'onRegistrationComplete',
			FOSUserEvents::REGISTRATION_CONFIRM => 'onRegistrationConfirm',
			KernelEvents::EXCEPTION => 'onKernelException',
		);
	}
	public function onRegistrationConfirm(GetResponseUserEvent $event)
	{
		//$user = $event->getUser();
		//$request = $event->getRequest();
	}
	public function onRegistrationInitialize(GetResponseUserEvent $event)
	{
		//$user = $event->getUser();
		//$request = $event->getRequest();
		
		if( $this->serviceContainer->get('app.app_helper')->getSetting('parameter_members_mode') == "false" ){
			return $event->setResponse(
				new RedirectResponse( $this->router->generate('site_index'))
			);
		}
	}
	public function onRegistrationSuccess(FormEvent $event)
	{
		//$user = $event->getUser();
		//$request = $event->getRequest();
	}
	public function onRegistrationComplete(FilterUserResponseEvent $event)
	{
		/*
		$user = $event->getUser();
        $this->serviceContainer->get('app.app_helper')->sendEmailBySetting(
        	$user->getEmail(), 
        	'register_email_subject', 
        	'register_email', 
        	true,
        	array('user' => $user),
        	array()
        );
        */
	}
	public function onKernelException(GetResponseForExceptionEvent $event)
	{
		
		if( isset($this->form) ){
			
			$error = new FormError($event->getException()->getMessage());
			
			$event->setResponse(
				new Response(
					$this->serviceContainer->get('templating')->render(
						'@FOSUser/Registration/register.html.twig', 
						array('form'=>$this->form->createView())
					)
				)
			);
			
		}
		return false;
		
	}

}
