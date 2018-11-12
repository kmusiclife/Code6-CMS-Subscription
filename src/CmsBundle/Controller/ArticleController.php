<?php

namespace CmsBundle\Controller;

use CmsBundle\Entity\Article;
use CmsBundle\Form\Type\ArticleFormType;

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
 * @Route("admin/article")
 */
class ArticleController extends Controller
{
    /**
     * @Route("/", name="article_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        
        $pager = $this->get('app.app_pager');
        $pager->setInc(10);
        $pager->setPath('article_index'); 
        
        $articles = $pager->getRepository( 'CmsBundle:Article', array(), array('id' => 'DESC') );

        return $this->render('CmsBundle:Article:index.html.twig', array(
	        'pager' => $pager,
            'articles' => $articles,
        ));
    }
	
    /**
     * @Route("/new", name="article_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
	    $em = $this->getDoctrine()->getManager();
	    $helper = $this->get('cms.cms_helper');
	    
	    $user = $this->getUser();
        $article = new Article();
        $seo = new Seo();
        
        $seo->setImage( new Image() );
        $article->setSeo( $seo );
        
        $image_ids = $this->get('cms.cms_helper')->getImageIds();
        
        foreach($image_ids as $image_id)
        {
	        $image_obj = new Image();
	        $image_obj->setTitle();
	        $article->getImages()->add($image_obj);
        }
        
        $form = $this->createForm(ArticleFormType::class, $article);
        $form->handleRequest($request);
        
		$helper->validImage($article->getSeo()->getImage(), $form['seo']['image']);
		
		if($article->getImages()){
	        foreach( $article->getImages() as $image ){
		        if(!$image->getFile()) $article->removeImage($image);
	        }
			$helper->validationImages($form['images'], $article->getImages());
	    }
		
        if( $form->isSubmitted() && $form->isValid() )
        {
	        $helper->uploadImage($article->getSeo()->getImage());
	        $helper->createImage($article->getSeo()->getImage(), $article->getTitle(), $article->getBody());
	        $helper->uploadImages($article->getImages());
	        foreach($article->getImages() as $image){
		        $helper->createImage($image, $article->getTitle(), $article->getBody());
	        }
	        $em->persist($article->getSeo());
            $em->persist($article);
            $em->flush();
            
            $this->addFlash('notice', 'message.added');
            return $this->redirectToRoute('article_edit', array('id' => $article->getId()));
			
        }
        
        return $this->render('CmsBundle:Article:new.html.twig', array(
            'article' => $article,
            'form' => $form->createView(),
        ));
    }
	
    /**
     * @Route("/{id}/edit", name="article_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction($id, Request $request, Article $article)
    {

        $em = $this->getDoctrine()->getManager();
        $helper = $this->get('cms.cms_helper');
        
        $image_ids = $this->get('cms.cms_helper')->getImageIds();
        
        foreach($image_ids as $i => $image_id)
        {
	        if(!isset($article->getImages()[$i]) )
	        {
		        $image_obj = new Image();
		        $image_obj->setTitle();
		        $article->getImages()->add($image_obj);
	        }
        }
        
        $deleteForm = $this->createDeleteForm($article);
        $editForm = $this->createForm('CmsBundle\Form\Type\ArticleEditFormType', $article);
        $editForm->handleRequest($request);
		
		if($editForm->isSubmitted()){
			
			$current_seo_image_filename = $article->getSeo()->getImage()->getSrc();
			$helper->validImage($article->getSeo()->getImage(), $editForm['seo']['image']);
			
			if($article->getImages()){
		       
		        foreach( $article->getImages() as $image ){
			        if(!$image->getFile() and !$image->getSrc()) {
				        $article->removeImage($image);
				    }
		        }
				$helper->validationImages($editForm['images'], $article->getImages());
				
		    }
		    
	        if ($editForm->isValid()) {
				
				if($article->getSeo()->getImage()->getFile()){
			        
			        $helper->uploadImage($article->getSeo()->getImage());
			        $helper->createImage($article->getSeo()->getImage(), $article->getTitle(), $article->getBody());
				    $helper->deleteImageFromFilename($current_seo_image_filename);
				    
			    } elseif(!$article->getSeo()->getImage()->getSrc()) {
				    $article->getSeo()->setImage(null);
			    }
				
		        $helper->uploadImages($article->getImages());
		        foreach($article->getImages() as $image){
			        $helper->createImage($image, $article->getTitle(), $article->getBody());
		        }
	            
	            $em = $this->getDoctrine()->getManager();
	            $em->persist($article);
	            $em->flush();
				
				$this->addFlash('notice', 'message.edited');
				return $this->redirectToRoute('article_edit', array('id' => $article->getId()));
	            
	        }

		}
        
        return $this->render('CmsBundle:Article:edit.html.twig', array(
            'article' => $article,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
	
    /**
     * @Route("/{id}", name="article_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Article $article)
    {
		
		$em = $this->getDoctrine()->getManager();
		$helper = $this->get('cms.cms_helper');
		
        $form = $this->createDeleteForm($article);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            
            $images = $article->getImages();
            
            $em->remove($article);
            
            $seo = $article->getSeo();
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
		
        return $this->redirectToRoute('article_index');
    }

    private function createDeleteForm(Article $article)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('article_delete', array('id' => $article->getId())))
            ->setMethod('DELETE')
            ->add('password', PasswordFormType::class)
            ->getForm()
        ;
    }
}
