<?php

namespace AppBundle\EventListener;

use Doctrine\Common\Persistence\ObjectManager;
use Oneup\UploaderBundle\Event\PostPersistEvent;
use CmsBundle\Entity\Image;

use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\ORM\EntityManagerInterface;


class UploadListener
{
    /**
     * @var ObjectManager
     */
	protected $serviceContainer;
	protected $tokenStorage;
	protected $entityManager;
	
	protected $user;

    public function __construct(
		ContainerInterface $serviceContainer, 
		TokenStorageInterface $tokenStorage,
		EntityManagerInterface $entityManager
    )
    {
		$this->serviceContainer = $serviceContainer;
		$this->tokenStorage = $tokenStorage;
		$this->EntityManager = $entityManager;
		
		if( is_object($this->tokenStorage->getToken()) )
			$this->user = $this->tokenStorage->getToken()->getUser();
		else $this->user = null;
		
    }
    public function onUpload(PostPersistEvent $event)
    {

        $image = new Image();
        $image->setSrc( $event->getFile()->getFilename() );
        $image->setCreatedUser( $this->user );
        $this->EntityManager->persist($image);
        $this->EntityManager->flush();

        $response = $event->getResponse();
        $response['success'] = true;
        
        return $response;
    }
}