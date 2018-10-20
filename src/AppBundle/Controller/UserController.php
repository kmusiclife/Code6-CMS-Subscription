<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\Type\PasswordFormType;

/**
 * @Route("admin/user")
 */
class UserController extends Controller
{
    /**
     * @Route("/", name="user_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $pager = $this->get('app.app_pager');
        $pager->setInc(10);
        $pager->setPath('user_index'); 
        
        $users = $pager->getRepository( 'AppBundle:User', array(), array('id' => 'DESC') );

        return $this->render('@AppBundle/Resources/views/User/index.html.twig', array(
	        'pager' => $pager,
            'users' => $users,
        ));
    }

    /**
     * @Route("/{id}", name="user_show")
     * @Method("GET")
     */
    public function showAction(User $user)
    {
        return $this->render('@AppBundle/Resources/views/User/show.html.twig', array(
            'user' => $user
        ));
    }
    /**
     * @Route("/{id}/edit", name="user_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, User $user)
    {
	    
	    $is_admin = in_array('ROLE_ADMIN', $user->getRoles());
	    
	    $deleteForm = $this->createDeleteForm($user);
        $editForm = $this->createForm('AppBundle\Form\Type\UserFormType', $user);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
	        
	        $userManager = $this->get('fos_user.user_manager');
            $userManager->updateUser($user);
            
            return $this->redirectToRoute('user_index');
        }

        return $this->render('@AppBundle/Resources/views/User/edit.html.twig', array(
            'user' => $user,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'is_admin' => $is_admin
        ));
    }

    /**
     * @Route("/{id}/delete", name="user_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, User $user)
    {
	    
	    $is_admin = in_array('ROLE_ADMIN', $user->getRoles());
	    
	    if($is_admin) {
	    	return $this->redirectToRoute('user_index');
	    }
        $form = $this->createDeleteForm($user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($user);
            $em->flush();
        }

        return $this->redirectToRoute('user_index');
    }

    /**
     * @param User $user The user entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(User $user)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('user_delete', array('id' => $user->getId())))
            ->setMethod('DELETE')
            ->add('password', PasswordFormType::class)
            ->getForm()
        ;
    }


}
