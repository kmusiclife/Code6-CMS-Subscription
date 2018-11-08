<?php

namespace CmsBundle\Helper;

use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\File\UploadedFile;

// Injection Classes
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

// Entities
use AppBundle\Entity\Setting;
use CmsBundle\Entity\Image;
use CmsBundle\Entity\Article;

// on Source
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

class CmsHelper 
{
	
	protected $serviceContainer;
	protected $tokenStorage;
	protected $userManager;
	protected $entityManager;
	protected $router;
	
	protected $user;
	
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

	public function getImageIds()
	{
		$max =  (int)$this->serviceContainer->getParameter('image_count');
		$images = array();
		
		for($i=0; $i<$max; $i++){
			array_push($images, 'image'.($i+1));
		}
		return $images;
		
	}
	public function validImage(Image $image, &$form_obj)
	{
		if( $image->getFile() ){
	        $this->validationImage($form_obj, $image);
		}
	}
	public function validationImages(&$form_obj, $images){
		
	    foreach( $images as $i => $image )
        {
	        
	        if(!$image->getFile()) continue;
			$errors = $this->serviceContainer->get('validator')->validate($image);
	        
	        if( count($errors) > 0 ){
		        foreach($errors as $error){
			        $form_obj[$i]['file']->addError( new FormError($error->getMessage()) );
		        }
	        } else {
			    $image_name = uniqid().'.'.$image->getFile()->guessExtension();
		        $image->setSrc($image_name);
	        }
        }

	}
	public function validationImage(&$form_obj, Image $image)
	{
		
		if(null == $image->getFile()) return;
        $errors = $this->serviceContainer->get('validator')->validate($image);
        
        if( count($errors) > 0 ){
	        foreach($errors as $error){
		        $form_obj['file']->addError( new FormError($error->getMessage()) );
	        }
        } else {
			$image_name = uniqid().'.'.$image->getFile()->guessExtension();
			$image->setSrc($image_name);
        }
        
	}
	public function uploadImage(Image $image)
	{
		if(!$image->getFile()) return;
	    return $image->getFile()->move($this->serviceContainer->getParameter('upload_path'), $image->getSrc());
	}
	public function uploadImages($images) 
	{
		$result_images = array();
	    foreach( $images as $image ){
			array_push($result_images, $this->uploadImage($image));
        }
        return $result_images;
	}
	public function deleteImage(Image $image)
	{
		$file_system = new Filesystem();
		$filename = $image->getSrc();
		
		if($this->deleteImageFromFilename($filename)) return $image;
		
		return false;
		
	}
	public function deleteImageFromFilename($filename)
	{
		if(!$filename) return false;
		$file_system = new Filesystem();
		
		try {
			$file_system->remove($this->serviceContainer->getParameter('upload_path').'/'.$filename);
            
		} catch (IOExceptionInterface $exception) {
			return false;
		}
		return true;
		
	}
	public function deleteImages($images)
	{
		$image_results = array();
		foreach($images as $image){
			array_push($image_results, $this->deleteImage($image));
		}
		return $image_results;
	}
	
}