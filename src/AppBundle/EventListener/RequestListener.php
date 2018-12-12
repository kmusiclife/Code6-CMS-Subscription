<?php

namespace AppBundle\EventListener;

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
        $path_info = $event->getRequest()->getPathInfo();
        if( preg_match('/^\/config\/user\//', $path_info) ) return;
        if( preg_match('/\/_wdt\//', $path_info) ) return;
        
        if( 
            !$this->serviceContainer->get('app.app_helper')->getSetting('parameters') or 
            $this->serviceContainer->get('app.app_helper')->getSetting('parameters') == "false"
        ){
            $this->serviceContainer->get('app.app_helper')->setSetting('parameter_demo_mode', "true");
            $this->serviceContainer->get('app.app_helper')->setSetting('parameter_admin_theme_name', "default");
            $this->serviceContainer->get('app.app_helper')->setSetting('parameter_theme_name', "default");
            $this->serviceContainer->get('app.app_helper')->setSetting('parameter_members_mode', "false");
            $this->serviceContainer->get('app.app_helper')->setSetting('parameter_image_count', 4);
            $this->serviceContainer->get('app.app_helper')->setSetting('parameter_invitation_mode', "false");
            $this->serviceContainer->get('app.app_helper')->setSetting('parameters', "true");
        }
        if( !$this->serviceContainer->get('app.init_helper')->checkUsers() ){
            $response = $this->serviceContainer->get('app.init_helper')->initUsers();
            if($response) return $event->setResponse($response);
        }

        if(null == $this->serviceContainer->get('app.app_helper')->getSetting('parameter_demo_mode')){
            $this->serviceContainer->get('app.app_helper')->setSetting('parameter_demo_mode', "true");
        }
        $is_demo_mode = $this->serviceContainer->get('app.app_helper')->getSetting('parameter_demo_mode') == "true" ? true : false;

        if( $is_demo_mode ){
            
            $user = $this->tokenStorage->getToken()->getUser();
            $is_granted = $this->serviceContainer->get('security.authorization_checker')->isGranted('ROLE_USER');
            $request = $event->getRequest();
            
            if( false == $is_granted and !preg_match('/\/login$|\/ogin_check$|\/_login$/', $path_info) )
            {
                $login_url = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBaseURL(). '/login';
                $event->setResponse(new RedirectResponse($login_url));
            }
        
        }

    }
}