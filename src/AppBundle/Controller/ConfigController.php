<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;

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
use AppBundle\Entity\Setting;
use AppBundle\Helper\InitHelper;

/**
 * Config controller.
 *
 * @Route("/")
 */
class ConfigController extends Controller
{
	
    /**
     * @Route("admin/config/setting/{slug}", name="setting_config")
     * @Method({"GET", "POST"})
     */
    public function configSettingAction(Request $request)
    {

	    $em = $this->getDoctrine()->getManager();
	    $slug = $request->get('slug');
	    $allow_slugs = InitHelper::getSettingSlugs();
	    $allow_slug_flag = false;
	    
	    foreach($allow_slugs as $allow_slug){
		    if($allow_slug == $slug) $allow_slug_flag = true;
	    }
	    if(false == $allow_slug_flag) {
		    throw new HttpNotFoundException("Page not found");
	    }
	    
	    $setting = $em->getRepository('AppBundle:Setting')->findOneBySlug($slug);
	    if($setting) return $this->redirectToRoute('admin_index');
	    
	    $setting = new Setting();
		$setting->setSlug( $request->get('slug') );
		$default_value = $this->get('translator')->trans('setting.default.'.$request->get('slug'), [], 'default');
		$setting->setValue($default_value);
        
        $form = $this->createForm('AppBundle\Form\Type\SettingRequireFormType', $setting)->remove('slug')->add('slug', HiddenType::class);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($setting);
            $em->flush();
            
            return $this->redirectToRoute('admin_index');
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
		
		if( $this->get('app.app_helper')->hasAdmin() > 0 ){
			return $this->redirectToRoute('admin_index');
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
        return $this->render('@AppBundle/Resources/views/Config/new.html.twig', array(
            'user' => $user,
            'form' => $form->createView(),
        ));
        
    }
    	
}
