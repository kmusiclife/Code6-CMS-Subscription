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
		$redirect_uri = $this->router->generate('stripe_redirect', [], UrlGeneratorInterface::ABSOLUTE_URL);
		$user = $this->getUser();
		
		$queries = array(
			'response_type' => 'code',
			'scope' => 'read_write',
			'client_id' => $this->serviceContainer->getParameter('stripe_connect_client_id'),
			'stripe_user[url]' => $redirect_uri,
			'stripe_user[email]' => $this->serviceContainer->getParameter('stripe_email'),
			'stripe_user[zip]' => $this->serviceContainer->getParameter('stripe_zip'),
			'stripe_user[phone_number]' => $user->getTel(),
			'stripe_user[first_name]' => $user->getFname(),
			'stripe_user[last_name]' => $user->getLname(),
			'redirect_uri' => $redirect_uri,
		);
		return $oauth_url.'?'.http_build_query( $queries );
		
	}
	
	
}