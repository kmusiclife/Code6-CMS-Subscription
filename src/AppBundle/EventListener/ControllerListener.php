<?php

namespace AppBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use AppBundle\Entity\User;

class ControllerListener
{
	
    protected $serviceContainer;
    
    public function __construct(ContainerInterface $serviceContainer)
    {
        $this->serviceContainer = $serviceContainer;
    }
    public function onKernelController(FilterControllerEvent $event){
	    
    }
    public function prePersist(LifecycleEventArgs $args)
    {

        $entity = $args->getEntity();
        
        if (property_exists($entity, 'createdAt')) {
            $entity->setCreatedAt(new \DateTime());
        }
        if (property_exists($entity, 'updatedAt')) {
            $entity->setUpdatedAt(new \DateTime());
        }
                
    }
    public function preUpdate(LifecycleEventArgs $args)
    {
        
        $entity = $args->getEntity();
        $em = $args->getEntityManager();
        
        if (property_exists($entity, 'updatedAt'))
        {
            $entity->setUpdatedAt(new \DateTime());
        }
        
    }
    public function postPersist(LifecycleEventArgs $args){}
    public function postUpdate(LifecycleEventArgs $args){}
    public function postRemove(LifecycleEventArgs $args){}
    public function preRemove(LifecycleEventArgs $args){}
    public function postLoad(LifecycleEventArgs $args){}
    
}