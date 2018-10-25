<?php

namespace CmsBundle\Controller;

use CmsBundle\Entity\Page;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

use AppBundle\Form\Type\PasswordFormType;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;

/**
 * @Route("/admin")
 */
class PageController extends Controller
{

    /**
     * @Route("/page", name="page_index")
     * @Method("GET")
     */
    public function indexAction()
    {
	    
	    $em = $this->getDoctrine()->getManager();

        $pager = $this->get('app.app_pager');
        $pager->setInc(10);
        $pager->setPath('page_index'); 
		$pages = $pager->getRepository('CmsBundle:Page', array(), array('id' => 'DESC') );
	    
        return $this->render('@CmsBundle/Resources/views/Page/index.html.twig', array(
	        'pager' => $pager,
	        'pages' => $pages
        ));

    }
    /**
     * @Route("/page/new", name="page_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $page = new Page();
        $form = $this->createForm('CmsBundle\Form\Type\PageFormType', $page);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($page);
            $em->flush();
			$this->addFlash('notice', 'message.added');
            return $this->redirectToRoute('page_index');
        }
        return $this->render('@CmsBundle/Resources/views/Page/new.html.twig', array(
            'page' => $page,
            'form' => $form->createView(),
        ));

    }
    /**
     * @Route("/page/edit/{slug}", name="page_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Page $page)
    {
        $deleteForm = $this->createDeleteForm($page);
        $editForm = $this->createForm('CmsBundle\Form\Type\PageFormType', $page);
        $editForm->handleRequest($request);
		
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            
            $this->getDoctrine()->getManager()->flush();
            
            $this->addFlash('notice', 'message.edited');
            return $this->redirectToRoute('page_index');
        }

        return $this->render('@CmsBundle/Resources/views/Page/edit.html.twig', array(
            'page' => $page,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * @Route("/page/delete/{id}", name="page_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Page $page)
    {
        $form = $this->createDeleteForm($page);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($page);
            $em->flush();
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
