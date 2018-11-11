<?php

namespace AppBundle\EventListener;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

use Symfony\Component\Security\Core\Exception\BadCredentialsException;


class LoginListener {
    
    
	protected $serviceContainer;
    protected $userManager;
    
    public function __construct(
		ContainerInterface $serviceContainer,
    	UserManagerInterface $userManager,
		UrlGeneratorInterface $router
    ){
		$this->serviceContainer = $serviceContainer;
        $this->userManager = $userManager;
		$this->router = $router;
    }
    
    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
	    $_recaptcha = $event->getRequest()->request->get('_recaptcha');
	    
        $recaptcha_json = $this->serviceContainer->get('app.app_helper')->curlRequest(
        	'https://www.google.com/recaptcha/api/siteverify',
        	array(
	        	'response' => $_recaptcha,
				'secret' => $this->serviceContainer->getParameter('recaptcha_secret_key')
			)
		);
        $recaptcha = json_decode($recaptcha_json);
        
        if(!$recaptcha->success)
        {
			throw new BadCredentialsException('Error');
        }
        
    }
}