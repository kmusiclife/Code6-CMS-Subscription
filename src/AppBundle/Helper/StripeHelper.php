<?php

namespace AppBundle\Helper;

use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class StripeHelper 
{
	
	protected $serviceContainer;
	
	public function __construct(
		ContainerInterface $serviceContainer,
		UrlGeneratorInterface $router
	){
		$this->serviceContainer = $serviceContainer;
		$this->router = $router;
	}
	public function setApiKey()
	{
		if(!$this->getSecretKey()) return false;
		\Stripe\Stripe::setApiKey( $this->getSecretKey() );
		return true;
	}
	public function getSecretKey()
	{
		return $this->serviceContainer->get('app.app_helper')->getSetting('stripe_secret_token');
	}
	public function getPublicKey()
	{
		return $this->serviceContainer->get('app.app_helper')->getSetting('stripe_public_token');
	}
	public function oauthGenerateUrl()
	{
		
		/* https://stripe.com/docs/connect/oauth-reference */

		$oauth_url = 'https://connect.stripe.com/oauth/authorize';
		$redirect_uri = $this->router->generate('stripe_oauth', [], UrlGeneratorInterface::ABSOLUTE_URL);
		
		$queries = array(
			'response_type' => 'code',
			'scope' => 'read_write',
			'client_id' => $this->serviceContainer->getParameter('stripe_connect_client_id'),
			'stripe_user[url]' => $redirect_uri,
			'stripe_user[email]' => $this->serviceContainer->getParameter('stripe_email'),
			'stripe_user[zip]' => $this->serviceContainer->getParameter('stripe_zip'),
			'redirect_uri' => $redirect_uri,
//			'stripe_user' => $this->serviceContainer->getParameter('your_email'),
		);
		return $oauth_url.'?'.http_build_query( $queries );
		
	}
	
	
}