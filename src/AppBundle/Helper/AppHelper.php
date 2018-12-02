<?php

namespace AppBundle\Helper;

// Injection Classes
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

// Entities
use AppBundle\Entity\Setting;
use CmsBundle\Entity\Image;
use CmsBundle\Entity\Article;

// on Source
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

use Symfony\Component\Finder\Finder;

class AppHelper 
{
	
	protected $serviceContainer;
	protected $tokenStorage;
	protected $userManager;
	protected $entityManager;
	protected $router;
	
	protected $user;
	protected $settings_cache;
	
	public function __construct(
		ContainerInterface $serviceContainer, 
		TokenStorageInterface $tokenStorage,
		UserManagerInterface $userManager, 
		EntityManagerInterface $entityManager, 
		UrlGeneratorInterface $router
	){
		
		$this->serviceContainer = $serviceContainer;
		$this->tokenStorage = $tokenStorage;
		$this->userManager = $userManager;
		$this->entityManager = $entityManager;
		$this->router = $router;
		
		$this->settings_cache = array();
		$this->user = $this->tokenStorage->getToken()->getUser();
	}
	public function theme_name()
	{
		$user = $this->tokenStorage->getToken()->getUser();
		if(is_object($user)){
			if($user->getTheme()) return $user->getTheme();
		}
		
		$theme_name = $this->serviceContainer->get('app.app_helper')->getSetting('parameter_theme_name');
		if($theme_name) return $theme_name;
		return $this->serviceContainer->get('app.app_helper')->setSetting('parameter_theme_name', "default");
	}
	public function curlRequest($url, $params=array()){
		
		if(!$url) return;
		
		$curl = curl_init($url);
		
		curl_setopt($curl,CURLOPT_POST, TRUE);
		curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($params));
		curl_setopt($curl,CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($curl,CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($curl,CURLOPT_COOKIEJAR,      'cookie');
		curl_setopt($curl,CURLOPT_COOKIEFILE,     'tmp');
		curl_setopt($curl,CURLOPT_FOLLOWLOCATION, TRUE);
		
		return curl_exec($curl);
		
	}
	public function getParameter($name)
	{
		return $this->serviceContainer->getParameter($name);
	}
	public function getSetting($slug)
	{
		if( isset($this->settings_cache[$slug]) ) return $this->settings_cache[$slug];

		$setting = $this->entityManager->getRepository('AppBundle:Setting')->findOneBySlug($slug);

		if($setting){
			$this->settings_cache[$slug] = $setting->getValue();
		} else return null;
		
		if(!$this->settings_cache[$slug]) return null;
		
		return $this->settings_cache[$slug];
		
	}
	public function updateSettingCache($slug, $value)
	{
		$this->settings_cache[$slug] = $value;
	}
	public function setSetting($slug, $value=null)
	{
		$setting = $setting = $this->entityManager->getRepository('AppBundle:Setting')->findOneBySlug($slug);
		if(!$setting) $setting = new Setting();
		
		$setting->setSlug($slug);
		$setting->setValue($value);
		
		$this->entityManager->persist($setting);
		$this->entityManager->flush();
		$this->updateSettingCache($slug, $value);

		return $setting->getValue();
	}
	public function setSettings($key, $parameters)
	{
		foreach($parameters as $slug=>$value)
		{
			$setting_slug = $key.'_'.$slug;
			$this->setSetting($setting_slug, $value);
		}
		
	}
	public function renderSetting($setting_slug, $params = array())
	{
		$source = $this->getSetting($setting_slug);
		if(!$source) return null;
		
		$params['login'] = $this->router->generate('fos_user_security_login', array(), UrlGeneratorInterface::ABSOLUTE_URL);
		$params['resetting'] = $this->router->generate('fos_user_resetting_request', array(), UrlGeneratorInterface::ABSOLUTE_URL);
		$params['profile'] = $this->router->generate('fos_user_profile_show', array(), UrlGeneratorInterface::ABSOLUTE_URL);
		$params['homepage'] = $this->router->generate('site_index', array(), UrlGeneratorInterface::ABSOLUTE_URL);
		
		$env = new \Twig_Environment(new \Twig_Loader_Array());
		$template = $env->createTemplate($source);
		
		return $template->render($params);
		
	}
	public function sendEmailBySetting($to, $subject_slug, $body_slug, $bcc=false, $params=array())
	{
		$subject = $this->renderSetting($subject_slug, $params);
		$body = $this->renderSetting($body_slug, $params);
		return $this->sendEmail($to, $subject, $body, $params, $bcc);
	}
	public function sendEmail($to, $subject, $body, $params=array(), $bcc=false)
	{
		$message = \Swift_Message::newInstance()
		    ->setSubject( $subject )
		    ->setTo( $to )
		    ->setBody( $body )
		;
		if( isset($param['from']) ){
			$message->setFrom($param['from']);
		} else {
			$message->setFrom( $this->serviceContainer->getParameter('mailer_address') );
		}
		if($bcc){
			$message->setBcc( $this->serviceContainer->getParameter('mailer_address') );
		} else {
			if( isset($param['bcc']) ) $message->setBcc($param['bcc']);
		}
		if( isset($param['cc']) ){
			$message->setCc($param['cc']);
		}
		return $this->serviceContainer->get('mailer')->send($message);
		
	}
	public function getThemeNames()
	{
		$theme_dir = $this->serviceContainer->getParameter('project_dir').'/app/Resources/views/themes';

		$finder = new Finder();
		$finder->depth('== 0')->ignoreUnreadableDirs()->directories()->in( $theme_dir );
		
		$theme_names = array();
		foreach ($finder as $dir) {
			$theme_name = $dir->getRelativePathname();
			array_push($theme_names, $theme_name);
		}

		return $theme_names;
	}
}