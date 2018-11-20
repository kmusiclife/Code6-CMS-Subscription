<?php

namespace AppBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class TwigExtension extends AbstractExtension
{

    protected $serviceContainer;
    protected $requestStack;

    public function __construct(ContainerInterface $serviceContainer, RequestStack $requestStack)
    {
        $this->serviceContainer = $serviceContainer;
        $this->requestStack = $requestStack;
    }
    
    public function getFilters()
    {
        return array(
            new TwigFilter('upload_uri', array($this, 'upload_uri')),
            new TwigFilter('absolute_url', array($this, 'absolute_url')),            
        );
    }
    public function absolute_url($src)
    {
	    $request = $this->requestStack->getCurrentRequest();
	    return $request->getScheme() . '://' . $request->getHttpHost() . '/' .$src;
    }
    public function upload_uri($src)
    {
		return $this->serviceContainer->getParameter('upload_uri').'/'.$src;
    }

	public function getFunctions()
	{
	    return array(
	        new \Twig_SimpleFunction('is_home', array($this, 'is_home')),
	        new \Twig_SimpleFunction('get_template_directory_uri', array($this, 'get_template_directory_uri')),
	        new \Twig_SimpleFunction('template_exists', array($this, 'template_exists')),
	        
	        new \Twig_SimpleFunction('getSetting', array($this, 'getSetting')),
	        new \Twig_SimpleFunction('getParameter', array($this, 'getParameter')),
	    );
	}
	public function template_exists($filename)
	{
	    $theme_name = $this->serviceContainer->get('app.app_helper')->getSetting('parameter_theme_name');
	    $template_file = $this->serviceContainer->getParameter('project_dir').'/app/Resources/views/themes/'.$theme_name.'/'.$filename;
	    return file_exists($template_file);
	}
	public function get_template_directory_uri()
	{
		if( $this->serviceContainer->get('app.app_helper')->getSetting('parameter_theme_name') ){
			return '/themes/'.$this->serviceContainer->get('app.app_helper')->getSetting('parameter_theme_name');
		}
		return '/';
	}
	public function is_home($app)
	{
		return $app->getRequest()->getRequestUri() == '/' ? true : false;
	}
	public function getSetting($slug)
	{
		return $this->serviceContainer->get('app.app_helper')->getSetting($slug);
	}
	public function getParameter($name)
	{
		return $this->serviceContainer->get('app.app_helper')->getParameter($name);
	}

}