<?php

namespace AppBundle\Helper;

// Injection Classes
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class InitHelper 
{
	
	protected $userManager;
	protected $serviceContainer;
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
		
		$this->user = $this->tokenStorage->getToken()->getUser();
	}
	public function initAdmin()
	{
		if(!$this->serviceContainer->get('app.app_helper')->hasSuper()){
			return new RedirectResponse( $this->router->generate('config_super_user') );
		}
		if(!$this->serviceContainer->get('app.app_helper')->hasAdmin()){
			return new RedirectResponse( $this->router->generate('config_admin_user') );
		}
		return null;
	}
	static function getSettingSlugs(){
		return array(
			'parameter_members_mode', 'parameter_image_count', 'parameter_demo_mode', 
		);
	}
	public function hasSettings()
	{
		$slugs = $this->getSettingSlugs();
		foreach($slugs as $slug){
			if( !$this->serviceContainer->get('app.app_helper')->getSetting($slug) ){
				return false;
			}
		}
		return true;
	}
	public function initSettings()
	{
		$slugs = $this->getSettingSlugs();
		if( null == $this->serviceContainer->get('app.app_helper')->getSetting('parameter_theme_name') ){
			$this->serviceContainer->get('app.app_helper')->setSetting('parameter_theme_name', 'default');
		}
		if( null == $this->serviceContainer->get('app.app_helper')->getSetting('parameter_admin_theme_name') ){
			$this->serviceContainer->get('app.app_helper')->setSetting('parameter_admin_theme_name', 'default');
		}
		foreach($slugs as $slug){
			if( !$this->serviceContainer->get('app.app_helper')->getSetting($slug) ){
				return new RedirectResponse( $this->router->generate('setting_config', array('slug' => $slug)) );
			}
		}
		
		return null;
		
	}
	public function initSite()
	{
		if($this->initAdmin()) return $this->initAdmin();
		if($this->initSettings()) return $this->initSettings();
	}

	
}