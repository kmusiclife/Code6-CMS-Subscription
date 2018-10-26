<?php

namespace AppBundle\EventListener;

use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;

// source
use Symfony\Component\Form\FormError;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

// Injection Classes
use FOS\UserBundle\Util\TokenGeneratorInterface;
use FOS\UserBundle\Mailer\MailerInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class RegistrationListener implements EventSubscriberInterface
{

	protected $userManager;
	protected $serviceContainer;
	protected $entityManager;
	protected $router;
	
	protected $form;
	protected $user;

	public function __construct(
		MailerInterface $mailer,
		TokenGeneratorInterface $tokenGenerator, 
		ContainerInterface $serviceContainer, 
		UserManagerInterface $userManager, 
		EntityManagerInterface $entityManager, 
		UrlGeneratorInterface $router
	){
		$this->mailer = $mailer;
		$this->tokenGenerator = $tokenGenerator;
		$this->serviceContainer = $serviceContainer;
		$this->userManager = $userManager;
		$this->EntityManager = $entityManager;
		$this->router = $router;
		
		$this->form = null;
		$this->user = null;
	}

	public static function getSubscribedEvents()
	{
		return array(
			FOSUserEvents::REGISTRATION_INITIALIZE => 'onRegistrationInitialize',
			FOSUserEvents::REGISTRATION_SUCCESS => 'onRegistrationSuccess',
			FOSUserEvents::REGISTRATION_COMPLETED => 'onRegistrationComplete',
			FOSUserEvents::REGISTRATION_CONFIRM => 'onRegistrationConfirm',
			KernelEvents::EXCEPTION => 'onKernelException',
		);
	}
	public function onRegistrationConfirm(GetResponseUserEvent $event)
	{
		//$user = $event->getUser();
		//$request = $event->getRequest();
	}
	public function onRegistrationInitialize(GetResponseUserEvent $event)
	{
		//$user = $event->getUser();
		//$request = $event->getRequest();
		
		if( !$this->serviceContainer->get('app.stripe_helper')->setApiKey() )
		{
			return $event->setResponse( new RedirectResponse( $this->router->generate('stripe_config') ) );
		}
		if( $this->serviceContainer->get('app.app_helper')->hasAdmin() == 0 )
		{
			return $event->setResponse( new RedirectResponse( $this->router->generate('site_config') ) );
		}
		foreach(array('register_email_subject_join', 'register_email_join', 'register_email_subject_leave', 'register_email_leave') as $slug){
			if( !$this->serviceContainer->get('app.app_helper')->getSetting($slug) )
			{
				return $event->setResponse( new RedirectResponse( 
					$this->router->generate('setting_config', array('slug' => $slug)) ) 
				);
			}
		}
		$this->serviceContainer->get('twig')->addGlobal(
			'stripe_public_token', 
			$this->serviceContainer->get('app.stripe_helper')->getPublicKey() 
		);

	}
	public function onRegistrationSuccess(FormEvent $event)
	{
		//$user = $event->getUser();
		//$request = $event->getRequest();
	
		$this->form = $event->getForm();
		$this->user = $this->form->getData();
		
		try{
			
			$this->serviceContainer->get('app.stripe_helper')->setApiKey();
			
			$customer = \Stripe\Customer::create(array(
				"email" => $this->user->getEmail(),
				"description" => "",
				"source" => $this->user->getStripeTokenId(),
			));
			$this->user->setStripeCustomerId($customer->id);
	
			$subscription = \Stripe\Subscription::create(
				array(
					"customer" => $customer->id,
					"items" => array(
						array("plan" => $this->user->getStripePlanId()),
					),
					"application_fee_percent" => 10,
				),
				array(
					"stripe_account" => $this->serviceContainer->get('app.app_helper')->getSetting('access_token')
				)
			);
			$this->user->setStripeSubscriptionId($subscription->id);
			
			
		} catch (\Stripe\Error\RateLimit $e) {
			throw new Exception('クレジットカードの登録などの作業の間隔が速すぎのために処理できませんでした。時間を置いて登録してみてください。');
		} catch (\Stripe\Error\InvalidRequest $e) {
			throw new Exception('クレジットカード登録時にリクエストエラーが起こりました。何度も登録に失敗する場合は管理者にご連絡ください。');
		} catch (\Stripe\Error\Authentication $e) {
			throw new Exception('クレジットカード登録の接続に失敗しました。何度も登録に失敗する場合は管理者にご連絡ください。');
		} catch (\Stripe\Error\ApiConnection $e) {
			throw new Exception('クレジットカードAPIの接続に失敗しました。何度も登録に失敗する場合は管理者にご連絡ください。');
		} catch (\Stripe\Error\Base $e) {
			throw new Exception('クレジットカードの登録に失敗しました。有効期限などを確認してください。それでも解決しない場合はカード会社に確認してください。');
		} catch (Exception $e) {
			throw new Exception('クレジットカード登録時にシステムエラーが起こりました。何度も登録に失敗する場合は管理者にご連絡ください。');
		}
		
		/*
		if( false == $this->serviceContainer->getParameter('fos_user.registration.confirmation.enabled') )
		{
			$this->user->setConfirmationToken($this->tokenGenerator->generateToken());
			$this->mailer->sendConfirmationEmailMessage($this->user);
		}
		*/
		
	}
	public function onRegistrationComplete(FilterUserResponseEvent $event)
	{
		$user = $event->getUser();
        $this->serviceContainer->get('app.app_helper')->sendEmailBySetting(
        	$user->getEmail(), 
        	'register_email_subject_join', 
        	'register_email_join', 
        	array('user' => $user),
        	true
        );
	}
	public function onKernelException(GetResponseForExceptionEvent $event)
	{
		
		if( isset($this->form) ){
			
			$error = new FormError($event->getException()->getMessage());
			
			$event->setResponse(
				new Response(
					$this->serviceContainer->get('templating')->render(
						'@FOSUser/Registration/register.html.twig', 
						array('form'=>$this->form->createView())
					)
				)
			);
			
		}
		return false;
		
	}

}
