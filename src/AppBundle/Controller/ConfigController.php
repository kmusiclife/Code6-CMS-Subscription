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
    
    /**
     * @Route("admin/setting/{slug}/config", name="setting_config", requirements={"slug"="register_email_subject|register_email|registered_description|cancel_email_subject|cancel_email|cancel_description|canceled_description"})
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
     * @Route("/admin/stripe/config", name="stripe_config")
     * @Method("GET")
     */
    public function configStripeAction()
    {
		if( $this->get('app.stripe_helper')->setApiKey() ){
			return $this->redirectToRoute('site_index');
		}
        return $this->render('@AppBundle/Resources/views/Config/stripe.html.twig', array(
	        'stripe_secret_token' => substr($this->getParameter('stripe_secret_token'), 0, 15).'****************',
	        'stripe_connect_client_id' => substr($this->getParameter('stripe_connect_client_id'), 0, 15).'****************',
	        'stripe_application_fee' => $this->getParameter('stripe_application_fee')
        ));
	}
    /**
     * @Route("/admin/stripe/start", name="stripe_start")
     * @Method("GET")
     */
    public function startStripeAction()
    {
		if( $this->get('app.stripe_helper')->setApiKey() ){
			return $this->redirectToRoute('site_index');
		}
	    return $this->redirect( $this->get('app.stripe_helper')->oauthGenerateUrl() );
	}
    /**
     * @Route("/admin/stripe/redirect", name="stripe_redirect")
     * @Method("GET")
     */
    public function redirectStripeAction(Request $request)
    {
	    
		if( $this->get('app.stripe_helper')->setApiKey() ){
			return $this->redirectToRoute('site_index');
		}

	    $url = 'https://connect.stripe.com/oauth/token';

		$queries = array(
			'client_secret' => $this->getParameter('stripe_secret_token'),
			'code' => $request->get('code'),
			'grant_type' => 'authorization_code'
		);
		
		$curl_options = [
		    CURLOPT_POST => true,
		    CURLOPT_POSTFIELDS => http_build_query($queries),
		    CURLOPT_SSL_VERIFYPEER => true,
		    CURLOPT_RETURNTRANSFER => true
		];
		
		$curl = curl_init($url);
		curl_setopt_array($curl, $curl_options);
		$response = curl_exec($curl);
		curl_close($curl);
		
		$parameters = json_decode($response, JSON_OBJECT_AS_ARRAY);
		
		$stripe_livemode = $parameters['livemode'];
		$stripe_public_token = $parameters['stripe_publishable_key'];
		$stripe_secret_token = $parameters['access_token'];
		$stripe_access_token = $parameters['stripe_user_id'];
		
		$this->get('app.app_helper')->setSetting('stripe_livemode', $stripe_livemode);
		$this->get('app.app_helper')->setSetting('stripe_public_token', $stripe_public_token);
		$this->get('app.app_helper')->setSetting('stripe_secret_token', $stripe_secret_token);
		$this->get('app.app_helper')->setSetting('stripe_access_token', $stripe_access_token);
		
        return $this->redirectToRoute('site_index');
	    
    }


}
