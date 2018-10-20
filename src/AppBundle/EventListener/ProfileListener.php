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
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ProfileListener implements EventSubscriberInterface
{
	
	protected $serviceContainer;
	protected $userManager;
	protected $entityManager;
	protected $router;
	
	protected $form;
	protected $user;
	public function __construct(
		ContainerInterface $serviceContainer, 
		UserManagerInterface $userManager, 
		EntityManagerInterface $entityManager, 
		UrlGeneratorInterface $router
	){
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
			FOSUserEvents::PROFILE_EDIT_INITIALIZE => 'onProfileEditInitialize',
			FOSUserEvents::PROFILE_EDIT_SUCCESS => 'onProfileEditSuccess',
			FOSUserEvents::PROFILE_EDIT_COMPLETED => 'onProfileEditComplete',
			KernelEvents::EXCEPTION => 'onKernelException',
		);
	}

	public function onProfileEditInitialize(GetResponseUserEvent $event)
	{
	}
	public function onProfileEditSuccess(FormEvent $event)
	{
		// $this->form = $event->getForm();
		// $this->user = $this->form->getData();
	}
	public function onProfileEditComplete(FilterUserResponseEvent $event)
	{
		//$user = $event->getUser();
	}
	public function onKernelException(GetResponseForExceptionEvent $event)
	{
		
		if( isset($this->form) ){
			
			$error = new FormError($event->getException()->getMessage());
			
			$event->setResponse(
				new Response(
					$this->serviceContainer->get('templating')->render(
						'@FOSUser/Profile/edit.html.twig', 
						array('form'=>$this->form->createView())
					)
				)
			);
			
		}
		return false;
		
	}

}
