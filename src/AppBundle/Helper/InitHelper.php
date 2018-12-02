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
	public function initUsers()
	{
		if(!$this->serviceContainer->get('app.init_helper')->hasSuper()){
			return new RedirectResponse( $this->router->generate('config_super_user') );
		}
		if(!$this->serviceContainer->get('app.init_helper')->hasAdmin()){
			return new RedirectResponse( $this->router->generate('config_admin_user') );
		}
		return null;
	}
	public function checkUsers()
	{
		if($this->hasAdmin() > 0 and $this->hasSuper() > 0){
			return true;
		}
		return false;
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
	public function hasSuper()
	{
	    $qb = $this->entityManager->createQueryBuilder();
	    $qb->select('count(u)')
	        ->from('AppBundle:User', 'u')
	        ->where('u.roles LIKE :roles')
	        ->setParameter('roles', '%ROLE_SUPER_ADMIN%');
	
	    return (int)$qb->getQuery()->getSingleScalarResult();
	    
	}
}