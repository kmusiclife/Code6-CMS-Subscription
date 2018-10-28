<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

use AppBundle\Form\Type\PasswordFormType;
use AppBundle\Entity\Setting;

/**
 * Config controller.
 *
 * @Route("/")
 */
class ConfigController extends Controller
{
    /**
     * @Route("admin/setting/{slug}/config", name="setting_config", requirements={"slug"="register_email_subject|register_email|cancel_email_subject|cancel_email"})
     * @Method({"GET", "POST"})
     */
    public function configSettingAction(Request $request)
    {
	    $em = $this->getDoctrine()->getManager();
	    $slug = $request->get('slug');
	    
	    $setting = $em->getRepository('AppBundle:Setting')->findOneBySlug($slug);
	    if($setting) return $this->redirectToRoute('site_index');
	    
	    $setting = new Setting();
		$setting->setSlug( $request->get('slug') );
		$default_value = $this->get('translator')->trans('setting.default.'.$request->get('slug'), [], 'message');
		$setting->setValue($default_value);
        
        $form = $this->createForm('AppBundle\Form\Type\SettingRequireFormType', $setting);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($setting);
            $em->flush();
            return $this->redirectToRoute('site_index');
        }
		
        return $this->render('@AppBundle/Resources/views/Setting/config.html.twig', array(
            'setting' => $setting,
            'form' => $form->createView(),
        ));
    }
    /**
     * @Route("config/user", name="config_user")
     * @Method("GET,POST")
     */
    public function configUserAction(Request $request)
    {
		
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
            
			return $this->redirectToRoute('site_index');
			
        }
        return $this->render('@AppBundle/Resources/views/Config/new.html.twig', array(
            'user' => $user,
            'form' => $form->createView(),
        ));
        
    }
    	
}
