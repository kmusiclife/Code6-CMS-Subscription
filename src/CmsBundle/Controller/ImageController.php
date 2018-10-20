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
     * Lists all image entities.
     *
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

        return $this->render('@CmsBundle/Resources/views/Image/index.html.twig', array(
	        'pager' => $pager,
            'images' => $images,
        ));
    }

    /**
     * Creates a new image entity.
     *
     * @Route("/new", name="image_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        return $this->render('@CmsBundle/Resources/views/Image/new.html.twig', array(
        ));
    }

    /**
     * Displays a form to edit an existing image entity.
     *
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

        return $this->render('@CmsBundle/Resources/views/Image/edit.html.twig', array(
            'image' => $image,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a image entity.
     *
     * @Route("/{id}", name="image_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Image $image)
    {
	    
	    if($image->getIsLock()){
		    return $this->redirectToRoute('image_index');
	    }
	    
        $form = $this->createDeleteForm($image);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->get('app.app_helper')->deleteImage( $image );
        }

        return $this->redirectToRoute('image_index');
    }

    /**
     * Creates a form to delete a image entity.
     *
     * @param Image $image The image entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
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
