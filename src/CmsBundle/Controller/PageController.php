<?php

namespace CmsBundle\Controller;

use CmsBundle\Entity\Page;
use CmsBundle\Form\Type\PageFormType;

use CmsBundle\Entity\Image;
use CmsBundle\Entity\Seo;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormError;

use AppBundle\Form\Type\PasswordFormType;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Form\Form;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Route("admin/page")
 */
class PageController extends Controller
{
    /**
     * @Route("/", name="page_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        
        $pager = $this->get('app.app_pager');
        $pager->setInc(10);
        $pager->setPath('page_index'); 
        
        $pages = $pager->getRepository( 'CmsBundle:Page', array(), array('id' => 'DESC') );

        return $this->render('@CmsBundle/Resources/views/Page/index.html.twig', array(
	        'pager' => $pager,
            'pages' => $pages,
        ));
    }
	
    /**
     * @Route("/new", name="page_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
	    $em = $this->getDoctrine()->getManager();
	    $helper = $this->get('cms.cms_helper');
	    
	    $user = $this->getUser();
        $page = new Page();
        $seo = new Seo();
        
        $seo->setImage( new Image() );
        $page->setSeo( $seo );
        
        $image_ids = $this->get('cms.cms_helper')->getImageIds();
        
        foreach($image_ids as $image_id)
        {
	        $image_obj = new Image();
	        $image_obj->setTitle();
	        $page->getImages()->add($image_obj);
        }
        
        $form = $this->createForm(PageFormType::class, $page);
        $form->handleRequest($request);
        
		$helper->validImage($page->getSeo()->getImage(), $form['seo']['image']);
		
		if($page->getImages()){
	        foreach( $page->getImages() as $image ){
		        if(!$image->getFile()) $page->removeImage($image);
	        }
			$helper->validationImages($form['images'], $page->getImages());
	    }
		
        if( $form->isSubmitted() && $form->isValid() )
        {
	        $helper->uploadImage($page->getSeo()->getImage());
	        $helper->createImage($page->getSeo()->getImage(), $page->getTitle(), $page->getBody());
	        $helper->uploadImages($page->getImages());
	        foreach($page->getImages() as $image){
		        $helper->createImage($image, $page->getTitle(), $page->getBody());
	        }
	        $em->persist($page->getSeo());
            $em->persist($page);
            $em->flush();
            
            $this->addFlash('notice', 'message.added');
            return $this->redirectToRoute('page_edit', array('id' => $page->getId()));
			
        }
        
        return $this->render('@CmsBundle/Resources/views/Page/new.html.twig', array(
            'page' => $page,
            'form' => $form->createView(),
        ));
    }
	
    /**
     * @Route("/{id}/edit", name="page_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction($id, Request $request, Page $page)
    {

        $em = $this->getDoctrine()->getManager();
        $helper = $this->get('cms.cms_helper');
        
        $image_ids = $this->get('cms.cms_helper')->getImageIds();
        
        foreach($image_ids as $i => $image_id)
        {
	        if(!isset($page->getImages()[$i]) )
	        {
		        $image_obj = new Image();
		        $image_obj->setTitle();
		        $page->getImages()->add($image_obj);
	        }
        }
        
        $deleteForm = $this->createDeleteForm($page);
        $editForm = $this->createForm('CmsBundle\Form\Type\PageEditFormType', $page);
        $editForm->handleRequest($request);
		
		if($editForm->isSubmitted()){
			
			$current_seo_image_filename = $page->getSeo()->getImage()->getSrc();
			$helper->validImage($page->getSeo()->getImage(), $editForm['seo']['image']);
			
			if($page->getImages()){
		       
		        foreach( $page->getImages() as $image ){
			        if(!$image->getFile() and !$image->getSrc()) {
				        $page->removeImage($image);
				    }
		        }
				$helper->validationImages($editForm['images'], $page->getImages());
				
		    }
		    
	        if ($editForm->isValid()) {
				
				if($page->getSeo()->getImage()->getFile()){
			        
			        $helper->uploadImage($page->getSeo()->getImage());
			        $helper->createImage($page->getSeo()->getImage(), $page->getTitle(), $page->getBody());
				    $helper->deleteImageFromFilename($current_seo_image_filename);
				    
			    }
				
		        $helper->uploadImages($page->getImages());
		        foreach($page->getImages() as $image){
			        $helper->createImage($image, $page->getTitle(), $page->getBody());
		        }
	            
	            $em = $this->getDoctrine()->getManager();
	            $em->persist($page);
	            $em->flush();
				
				$this->addFlash('notice', 'message.edited');
				return $this->redirectToRoute('page_edit', array('id' => $page->getId()));
	            
	        }

		}
        
        return $this->render('@CmsBundle/Resources/views/Page/edit.html.twig', array(
            'page' => $page,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
	
    /**
     * @Route("/{id}", name="page_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Page $page)
    {
		
		$em = $this->getDoctrine()->getManager();
		$helper = $this->get('cms.cms_helper');
		
        $form = $this->createDeleteForm($page);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            
            $images = $page->getImages();
            
            $em->remove($page);
            
            $seo = $page->getSeo();
            if(is_object($seo->getImage())){
	            $seo_image = $helper->deleteImage( $seo->getImage() );
				$em->remove($seo_image);
            }
            if($seo) $em->remove($seo);
            
            $images = $helper->deleteImages( $images );            
            foreach($images as $image)
            {
	            $em->remove($image);
            }
            
            $em->flush();
			$this->addFlash('notice', 'message.deleted');
        }
		
        return $this->redirectToRoute('page_index');
    }

    private function createDeleteForm(Page $page)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('page_delete', array('id' => $page->getId())))
            ->setMethod('DELETE')
            ->add('password', PasswordFormType::class)
            ->getForm()
        ;
    }
}
