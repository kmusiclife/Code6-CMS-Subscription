<?php

namespace SubscriptionBundle\Helper;

// Injection Classes
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class StripeHelper 
{
	
	protected $serviceContainer;
	protected $tokenStorage;
	protected $router;
	
	public function __construct(
		ContainerInterface $serviceContainer,
		TokenStorageInterface $tokenStorage,
		UrlGeneratorInterface $router
	){
		$this->serviceContainer = $serviceContainer;
		$this->tokenStorage = $tokenStorage;
		$this->router = $router;
		
		$this->user = $this->tokenStorage->getToken()->getUser();
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
		
		$queries = array(
			'response_type' => 'code',
			'scope' => 'read_write',
			'client_id' => $this->serviceContainer->getParameter('stripe_connect_client_id'),
			'stripe_user[url]' => $redirect_uri,
			'stripe_user[phone_number]' => $this->user->getTel(),
			'stripe_user[first_name]' => $this->user->getFname(),
			'stripe_user[last_name]' => $this->user->getLname(),
			'redirect_uri' => $redirect_uri,
		);
		return $oauth_url.'?'.http_build_query( $queries );
		
	}
	
	
}