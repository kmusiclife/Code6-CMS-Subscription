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
use Symfony\Component\HttpFoundation\Session\Session;

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
		$this->entityManager = $entityManager;
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
		// $user = $event->getUser();

		if( $this->serviceContainer->get('app.app_helper')->getSetting('parameter_members_mode') == "false" ){
			return $event->setResponse(
				new RedirectResponse( $this->router->generate('site_index'))
			);
		}

		if( $this->serviceContainer->get('app.app_helper')->getSetting('parameter_invitation_mode') == "true" ){

			if( $this->serviceContainer->get('session')->get('invitation_passed') ) return;

			if( false == $this->serviceContainer->get('app.app_helper')->recaptchaCheck() ){
				$this->serviceContainer->get('session')->getFlashBag()->add('error', 'タイムアウトまたはスパム防止のためもう一度操作を行ってください');
				return $event->setResponse(
					new RedirectResponse( $this->router->generate('invitation_index_public'))
				);
			}

			$request = $event->getRequest();
			$code = $request->request->get('code');
			
			if(null == $code) {
				$this->serviceContainer->get('session')->getFlashBag()->add('error', '招待コードが入力されていません');
				return $event->setResponse(
					new RedirectResponse( $this->router->generate('invitation_index_public'))
				);
			}

			$invitation = $this->entityManager->getRepository('AppBundle:Invitation')->findOneBy(array('code' => $code));
			if(null == $invitation){
				$this->serviceContainer->get('session')->getFlashBag()->add('error', '招待コードが正しくありません正しい招待コードを入力してください');
				return $event->setResponse(
					new RedirectResponse( $this->router->generate('invitation_index_public'))
				);
			}

			if($invitation->getCountCurrent() >= $invitation->getCountLimit())
			{
				$this->serviceContainer->get('session')->getFlashBag()->add('error', '招待コードの上限を超えましたこの招待コードは使えません');
				return $event->setResponse(
					new RedirectResponse( $this->router->generate('invitation_index_public'))
				);
			}

			$this->serviceContainer->get('session')->set('invitation_passed', $invitation->getCode());

		}

	}
	public function onRegistrationSuccess(FormEvent $event)
	{
		if( $this->serviceContainer->get('app.app_helper')->getSetting('parameter_invitation_mode') == "true" ){
			$user = $event->getForm()->getData();
			$code = $this->serviceContainer->get('session')->get('invitation_passed');
			
			$invitation = $this->entityManager->getRepository('AppBundle:Invitation')->findOneBy(array('code' => $code));
			$user->setInvitation($invitation);
			$this->entityManager->persist($user);
			
			$invitation->setCountCurrent( $invitation->getCountCurrent() + 1 );
			$this->entityManager->persist($invitation);

			$this->entityManager->flush();
		}

	}
	public function onRegistrationComplete(FilterUserResponseEvent $event)
	{
		if( $this->serviceContainer->get('app.app_helper')->getSetting('parameter_invitation_mode') == "true" ){
			$this->serviceContainer->get('session')->set('invitation_passed', null);
		}
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
