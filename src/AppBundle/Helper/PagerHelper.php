<?php

namespace AppBundle\Helper;

// Injection Classes
use Symfony\Component\HttpFoundation\RequestStack;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class PagerHelper 
{
	
	protected $requestStack;
	protected $entityManager;
	protected $tokenStorage;

	protected $user;

    protected $inc;
    protected $offset;
    protected $count;
    protected $is_next = true;
    protected $path;
	
	public function __construct(
		RequestStack $requestStack,
		EntityManagerInterface $entityManager,
		TokenStorageInterface $tokenStorage
	){
		
		$this->requestStack = $requestStack;
		$this->entityManager = $entityManager;
		$this->tokenStorage = $tokenStorage;
		
		$request = $this->requestStack->getCurrentRequest();
		$this->offset = $request->get('offset');
		
		if(!$this->offset) $this->offset = 0;
		if(!preg_match('/^\d{1,}$/', $this->offset)) $this->offset = 0;
		
		$this->user = $this->tokenStorage->getToken()->getUser();

	}
	public function setPath($path){
		$this->path = $path;
	}    
	public function setInc($inc){
		$this->inc = $inc;
	}

	public function getArticles()
	{
		$now = new \DateTime("now");
		$qb = $this->entityManager->createQueryBuilder();
		$qb
			->select('e')
			->from('CmsBundle:Article', 'e')
            ->where('e.publishedAt <= :now')
            ->setParameter('now', $now)
            ->add('orderBy', 'e.id DESC')
            ->setFirstResult( $this->offset )
            ->setMaxResults( $this->inc )
		;
		
		if( is_object($this->user) ){
			
			if( in_array('ROLE_USER', $this->user->getRoles()) ){
				$qb->andWhere('e.is_member = true OR e.is_member = false');
			} else {
				$qb->andWhere('e.is_member = false');
			}

		} else {
			$qb->andWhere('e.is_member = false');
		}

		$articles = $qb->getQuery()->getResult();

	    $this->count = count($articles);
	    $this->is_next = $this->count >= $this->inc ? true : false;
	    
		return $articles;
		
	}
	public function getRepository($namespace, $where = array(), $orderby = array()){
	    
	    $entities = $this->entityManager->getRepository($namespace)->findBy($where, $orderby, $this->inc, $this->offset);
		
	    $this->count = count($entities);
	    $this->is_next = $this->count >= $this->inc ? true : false;
	    
	    return $entities;
	    
	}
    public function getVars(){
        
        return array(
            'next' => $this->offset + $this->inc, 
            'prev' => $this->offset - $this->inc, 
            'current' => $this->offset+1, 
            'is_next' => $this->is_next, 
            'inc' => $this->inc, 
            'count' => $this->count,
            'path' => $this->path
        );
    }
}