<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Entity\Setting;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Symfony\Component\Form\Extension\Core\Type\HiddenType;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\HttpKernel\Exception\HttpNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use AppBundle\Form\Type\PasswordFormType;

/**
 * Config controller.
 *
 * @Route("/")
 */
class ConfigController extends Controller
{
    /**
     * @Route("config/user/admin", name="config_admin_user")
     * @Method("GET,POST")
     */
    public function configAdminUserAction(Request $request)
    {
		if( $this->get('app.init_helper')->checkUsers() ){
			throw new NotFoundHttpException("Page not found");
		}
        $em = $this->getDoctrine()->getManager();

        $user = new User();
        $form = $this->createForm('AppBundle\Form\Type\ConfigUserFormType', $user);
        $form->handleRequest($request);
		
        if ($form->isSubmitted() && $form->isValid()) {
            
            $em = $this->getDoctrine()->getManager();
            $user->setEnabled(true);
            $user->addRole('ROLE_ADMIN');
            $em->persist($user);
            $em->flush();

            // Manual login
	        $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
	        $this->get('security.token_storage')->setToken($token);
            
			return $this->redirectToRoute('admin_index');
			
        }
        return $this->render('@AppBundle/Resources/views/Config/admin.html.twig', array(
            'user' => $user,
            'form' => $form->createView(),
        ));
    }
    /**
     * @Route("config/user/super", name="config_super_user")
     * @Method("GET,POST")
     */
    public function configSuperUserAction(Request $request)
    {
		if( $this->get('app.init_helper')->checkUsers() ){
			throw new NotFoundHttpException("Page not found");
		}
        $em = $this->getDoctrine()->getManager();

        $user = new User();
        $form = $this->createForm('AppBundle\Form\Type\ConfigUserFormType', $user);
        $form->handleRequest($request);
		
        if ($form->isSubmitted() && $form->isValid()) {
            
            $em = $this->getDoctrine()->getManager();
            $user->setEnabled(true);
            $user->addRole('ROLE_SUPER_ADMIN');
            $em->persist($user);
            $em->flush();
            
            // Manual login
	        $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
	        $this->get('security.token_storage')->setToken($token);
            
			return $this->redirectToRoute('admin_index');
			
        }
        return $this->render('@AppBundle/Resources/views/Config/super.html.twig', array(
            'user' => $user,
            'form' => $form->createView(),
        ));
    }    	
}
