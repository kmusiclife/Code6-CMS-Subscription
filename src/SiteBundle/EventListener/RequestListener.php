<?php
// src/EventListener/RequestListener.php
namespace SiteBundle\EventListener;

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
        if(null == $this->serviceContainer->get('app.app_helper')->getSetting('parameter_demo_mode')){
            $this->serviceContainer->get('app.app_helper')->setSetting('parameter_demo_mode', "false");
        }
        $is_demo_mode = $this->serviceContainer->get('app.app_helper')->getSetting('parameter_demo_mode') == "true" ? true : false;

        if( $is_demo_mode ){
            
            $user = $this->tokenStorage->getToken()->getUser();
            $is_granted = $this->serviceContainer->get('security.authorization_checker')->isGranted('ROLE_USER');
            $path_info = $event->getRequest()->getPathInfo();
            $request = $event->getRequest();
            
            if( preg_match('/\/_wdt\//', $path_info) ) return;
            if( false == $is_granted and !preg_match('/\/login$|\/ogin_check$|\/_login$/', $path_info) )
            {
                $login_url = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBaseURL(). '/login';
                $event->setResponse(new RedirectResponse($login_url));
            }
        
        }

    }
}