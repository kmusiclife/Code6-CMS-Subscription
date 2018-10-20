<?php

namespace AppBundle\Helper;

// Injection Classes
use Symfony\Component\HttpFoundation\RequestStack;
use Doctrine\ORM\EntityManagerInterface;

class PagerHelper 
{
	
	protected $requestStack;

    protected $inc;
    protected $offset;
    protected $count;
    protected $is_next = true;
    protected $path;
	
	public function __construct(
		RequestStack $requestStack,
		EntityManagerInterface $entityManager
	){
		
		$this->requestStack = $requestStack;
		$this->entityManager = $entityManager;
		
		$request = $this->requestStack->getCurrentRequest();
		$this->offset = $request->get('offset');
		
		if(!$this->offset) $this->offset = 0;
		if(!preg_match('/^\d{1,}$/', $this->offset)) $this->offset = 0;
		
	}
	public function setPath($path){
		$this->path = $path;
	}    
	public function setInc($inc){
		$this->inc = $inc;
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