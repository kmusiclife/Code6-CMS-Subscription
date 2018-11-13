<?php

namespace CmsBundle\Controller;

use CmsBundle\Entity\Contact;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

use AppBundle\Form\Type\PasswordFormType;

/**
 * Contact controller.
 *
 * @Route("admin/contact")
 */
class ContactController extends Controller
{
    /**
     * @Route("/", name="admin_contact_index")
     * @Method("GET")
     */
    public function indexAction()
    {

        $em = $this->getDoctrine()->getManager();
        
        $pager = $this->get('app.app_pager');
        $pager->setInc(10);
        $pager->setPath('admin_contact_index'); 
        
        $contacts = $pager->getRepository( 'CmsBundle:Contact', array(), array('id' => 'DESC') );

        return $this->render('CmsBundle:Contact:index.html.twig', array(
	        'pager' => $pager,
            'contacts' => $contacts,
        ));

    }
    /**
     * @Route("/{id}/edit", name="admin_contact_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Contact $contact)
    {
        $deleteForm = $this->createDeleteForm($contact);
        $editForm = $this->createForm('CmsBundle\Form\Type\ContactEditFormType', $contact);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('contact_edit', array('id' => $contact->getId()));
        }

        return $this->render('CmsBundle:Contact:edit.html.twig', array(
            'contact' => $contact,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * @Route("/{id}", name="admin_contact_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Contact $contact)
    {
        $form = $this->createDeleteForm($contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($contact);
            $em->flush();
        }

        $this->addFlash('notice', 'message.contact_deleted');
                
        return $this->redirectToRoute('admin_contact_index');
    }

    /**
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Contact $contact)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('admin_contact_delete', array('id' => $contact->getId())))
            ->setMethod('DELETE')
            ->add('password', PasswordFormType::class)
            ->getForm()
        ;
    }

}
