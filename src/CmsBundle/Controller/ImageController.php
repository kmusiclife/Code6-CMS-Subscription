<?php

namespace CmsBundle\Controller;

use CmsBundle\Entity\Image;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

use AppBundle\Form\Type\PasswordFormType;

/**
 * Image controller.
 *
 * @Route("admin/image")
 */
class ImageController extends Controller
{
    /**
     * @Route("/", name="image_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $pager = $this->get('app.app_pager');
        $pager->setInc(10);
        $pager->setPath('image_index'); 
        
        $images = $pager->getRepository( 'CmsBundle:Image', array(), array('id' => 'DESC') );

        return $this->render('CmsBundle::Image:index.html.twig', array(
	        'pager' => $pager,
            'images' => $images,
        ));
    }

    /**
     * @Route("/new", name="image_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        return $this->render('CmsBundle:Image:new.html.twig', array(
        ));
    }

    /**
     * @Route("/{id}/edit", name="image_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Image $image)
    {
        $deleteForm = $this->createDeleteForm($image);
        $editForm = $this->createForm('CmsBundle\Form\Type\ImageFormType', $image);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('image_edit', array('id' => $image->getId()));
        }

        return $this->render('CmsBundle:Image:edit.html.twig', array(
            'image' => $image,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * @Route("/{id}", name="image_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Image $image)
    {
	    $em = $this->getDoctrine()->getManager();
	    
	    if($image->getIsLock()){
		    return $this->redirectToRoute('image_index');
	    }
	    
        $form = $this->createDeleteForm($image);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->get('cms.cms_helper')->deleteImage( $image );
            $em->remove($image);
            $em->flush();
        }

        return $this->redirectToRoute('image_index');
    }

    private function createDeleteForm(Image $image)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('image_delete', array('id' => $image->getId())))
            ->setMethod('DELETE')
            ->add('password', PasswordFormType::class)
            ->getForm()
        ;
    }
}
