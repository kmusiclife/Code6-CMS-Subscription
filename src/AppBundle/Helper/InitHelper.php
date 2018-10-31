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

	public function hasAdmin()
	{
	    $qb = $this->entityManager->createQueryBuilder();
	    $qb->select('count(u)')
	        ->from('AppBundle:User', 'u')
	        ->where('u.roles LIKE :roles')
	        ->setParameter('roles', '%ROLE_ADMIN%');
	
	    return (int)$qb->getQuery()->getSingleScalarResult();
	    
	}
	public function initAdmin()
	{
		if(!$this->hasAdmin()){
			return new RedirectResponse( $this->router->generate('config_user') );
		}
		return null;
	}
	// Do not forget change CmpBundle:ConfigController also
	static function getPageSlugs(){
		return array('privacy', 'term');
	}
	public function initPage()
	{
		$page_slugs = $this->getPageSlugs();
		foreach($page_slugs as $page_slug){
			$page = $this->entityManager->getRepository('CmsBundle:Page')->findOneBySlug($page_slug);
			if( !$page ) return new RedirectResponse( $this->router->generate('site_config_page', array('slug' => $page_slug)));
		}
		return null;
		
	}
	// Do not forget change AppBundle:ConfigController also
	static function getSettingSlugs(){
		return array('register_email_subject', 'register_email', 'registered_description', 'cancel_email_subject', 'cancel_email', 'cancel_description', 'canceled_description');
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
		if($this->initPage()) return $this->initPage();
	}

	
}