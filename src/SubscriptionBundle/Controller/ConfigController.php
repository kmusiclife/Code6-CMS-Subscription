<?php

namespace SubscriptionBundle\Controller;

use AppBundle\Entity\User;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Symfony\Component\Form\Extension\Core\Type\HiddenType;

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
     * @Route("/admin/stripe/config", name="stripe_config")
     * @Method("GET")
     */
    public function configStripeAction()
    {
		if( $this->get('subscription.stripe_helper')->setApiKey() ){
			return $this->redirectToRoute('site_index');
		}
        return $this->render('SubscriptionBundle:Config:stripe.html.twig', array(
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
		if( $this->get('subscription.stripe_helper')->setApiKey() ){
			return $this->redirectToRoute('site_index');
		}
	    return $this->redirect( $this->get('subscription.stripe_helper')->oauthGenerateUrl() );
	}
    /**
     * @Route("/admin/stripe/redirect", name="stripe_redirect")
     * @Method("GET")
     */
    public function redirectStripeAction(Request $request)
    {
	    
		if( $this->get('subscription.stripe_helper')->setApiKey() ){
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
		
		$this->addFlash('notice', 'message.subscription.stripe.registered');
        return $this->redirectToRoute('admin_index');
	    
    }


}
