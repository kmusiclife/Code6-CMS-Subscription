<?php

namespace SubscriptionBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

// Injection
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class RequestListener
{
	protected $serviceContainer;
	protected $tokenStorage;
	protected $userManager;
	protected $entityManager;
	protected $router;
	
	public function __construct(
		ContainerInterface $serviceContainer, 
		TokenStorageInterface $tokenStorage,
		UserManagerInterface $userManager, 
		EntityManagerInterface $entityManager, 
		UrlGeneratorInterface $router
	){
		$this->serviceContainer = $serviceContainer;
		$this->tokenStorage = $tokenStorage;
		$this->userManager = $userManager;
		$this->entityManager = $entityManager;
		$this->router = $router;		
    }
    
    public function onKernelRequest(GetResponseEvent $event)
    {   
        if (false == $event->isMasterRequest()) return;
        if($this->serviceContainer->get('subscription.stripe_helper')->setApiKey()) return;

        $path_info = $event->getRequest()->getPathInfo();        
        if( preg_match('/^\/config\/user\/super$|^\/config\/user\/admin$|^\/admin\/stripe\/config$|^\/admin\/stripe\/start$|^\/admin\/stripe\/redirect(.*)$|^\/login$|\/ogin_check$|^\/_login$/', $path_info) ) return;
        if( preg_match('/\/_wdt\//', $path_info) ) return;
        
        if( $this->serviceContainer->get('security.authorization_checker')->isGranted('ROLE_ADMIN') ){
            return $event->setResponse( new RedirectResponse( $this->router->generate('stripe_config') ) );
        }

    }
}