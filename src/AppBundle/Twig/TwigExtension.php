<?php

namespace AppBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class TwigExtension extends AbstractExtension
{

    protected $serviceContainer;
    protected $requestStack;
    protected $entityManager;
    protected $router;
    
    protected $pager;

    public function __construct(
    	ContainerInterface $serviceContainer, 
    	RequestStack $requestStack,
        EntityManagerInterface $entityManager,
        UrlGeneratorInterface $router
    )
    {
        $this->serviceContainer = $serviceContainer;
        $this->requestStack = $requestStack;
        $this->EntityManager = $entityManager;
        $this->router = $router;
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
	        new \Twig_SimpleFunction('get_template_url', array($this, 'get_template_directory_uri')),
	        new \Twig_SimpleFunction('template_exists', array($this, 'template_exists')),
			new \Twig_SimpleFunction('template_path', array($this, 'template_path')),
			new \Twig_SimpleFunction('template_layout', array($this, 'template_layout')),
			new \Twig_SimpleFunction('get_header', array($this, 'get_header')),
	        new \Twig_SimpleFunction('get_footer', array($this, 'get_footer')),
	        new \Twig_SimpleFunction('get_part', array($this, 'get_part')),
	        new \Twig_SimpleFunction('get_posts', array($this, 'get_articles')),
			
	        new \Twig_SimpleFunction('get_pager', array($this, 'get_pager')),
	        new \Twig_SimpleFunction('get_pager_vars', array($this, 'get_pager_vars')),
	        new \Twig_SimpleFunction('have_new_articles', array($this, 'have_new_articles')),
	        new \Twig_SimpleFunction('get_articles', array($this, 'get_articles')),
	        new \Twig_SimpleFunction('get_article_embed', array($this, 'get_article_embed')),
	        new \Twig_SimpleFunction('article_index_permalink', array($this, 'article_index_permalink')),
	        new \Twig_SimpleFunction('article_permalink', array($this, 'article_permalink')),
	        new \Twig_SimpleFunction('article_image', array($this, 'article_image')),
			
	        new \Twig_SimpleFunction('getSetting', array($this, 'getSetting')),
	        new \Twig_SimpleFunction('getParameter', array($this, 'getParameter')),
	    );
	}
	public function template_layout()
	{
		$template_file = $this->template_path("layout.html.twig");
		if(null == $template_file) return $this->template_path("layout.html.twig", "default");
		return $template_file;
	}
	public function template_path($filename, $theme_name=null)
	{
		if(null == $theme_name){
			$theme_name = $this->serviceContainer->get('app.app_helper')->getSetting('parameter_theme_name');
		}
		$template_file = $this->serviceContainer->getParameter('project_dir').'/app/Resources/views/themes/'.$theme_name.'/'.$filename;
		if( file_exists($template_file) ) return $template_file;
		return false;
	}
	public function get_article_embed()
	{
	    if( $this->template_exists('_cms/article.index.embed.html.twig') ){
$theme_name = $this->serviceContainer->get('app.app_helper')->getSetting('parameter_theme_name');
		    $template_file = $this->serviceContainer->getParameter('project_dir').'/app/Resources/views/themes/'.$theme_name.'/_cms/article.index.embed.html.twig';
	    } else {
			$theme_name = $this->serviceContainer->get('app.app_helper')->getSetting('parameter_theme_name');
			$template_file = $this->serviceContainer->getParameter('project_dir').'/app/Resources/views/themes/default/_cms/article.index.embed.html.twig';
	    }
	    return $template_file;
	}
	public function article_image($article, $image_format='image_normal')
	{
		if(null == $article->getSeo()->getImage()->getSrc()) return null;
		$image = $this->serviceContainer->getParameter('upload_uri').'/'.$article->getSeo()->getImage()->getSrc();
		return $this->serviceContainer->get('liip_imagine.cache.manager')->getBrowserPath($image, $image_format);
	}
	public function article_permalink($article)
	{
		return $this->router->generate('article_show', array('slug' => $article->getSlug()));
	}
	public function article_index_permalink()
	{
		return $this->router->generate('article_index_public');
	}
	public function have_new_articles()
	{
		$article = $this->EntityManager->getRepository('CmsBundle:Article')->findOneBy(array(), array('createdAt' => 'DESC'));
		if($article){
			$current_date = new \DateTime();
			$interval = $current_date->diff( $article->getPublishedAt() );
			if( (int)$interval->format('%a') < 90 ) return true;
		}
		return false;
	}
	public function get_articles($limit=5)
	{
        $pager = $this->serviceContainer->get('app.app_pager');
        $pager->setInc($limit);
        $pager->setPath('article_index_public'); 
        $articles = $pager->getRepository( 'CmsBundle:Article', array(), array('id' => 'DESC') );
        
        $this->pager = $pager;
        
		return $articles;
	}
	public function get_pager()
	{
	    if( $this->template_exists('_cms/pager.html.twig') ){
		    $theme_name = $this->serviceContainer->get('app.app_helper')->getSetting('parameter_theme_name');
		    $template_file = $this->serviceContainer->getParameter('project_dir').'/app/Resources/views/themes/'.$theme_name.'/_cms/pager.html.twig';
	    } else {
			$theme_name = $this->serviceContainer->get('app.app_helper')->getSetting('parameter_theme_name');
			$template_file = $this->serviceContainer->getParameter('project_dir').'/app/Resources/views/themes/default/_cms/pager.html.twig';
	    }
	    return $template_file;
	}
	public function get_pager_vars()
	{
		return $this->pager;
	}
	public function get_part($extname='')
	{
	    return $this->get_template('part', $extname);
	}
	public function get_footer($extname='')
	{
	    return $this->get_template('footer', $extname);
	}
	public function get_header($extname='')
	{
	    return $this->get_template('header', $extname);
	}
	private function get_template($filename, $extname='')
	{
	    $theme_name = $this->serviceContainer->get('app.app_helper')->getSetting('parameter_theme_name');
	    $template_file = $this->serviceContainer->getParameter('project_dir').'/app/Resources/views/themes/'.$theme_name.'/'.$filename.($extname ? '-'.$extname : '').'.html.twig';
	    
	    return $template_file;
	}
	public function template_exists($filename)
	{
	    $theme_name = $this->serviceContainer->get('app.app_helper')->getSetting('parameter_theme_name');
	    $template_file = $this->serviceContainer->getParameter('project_dir').'/app/Resources/views/themes/'.$theme_name.'/'.$filename;
	    $exists = file_exists($template_file);
	    if(false == $exists){
		    if(false == file_exists($this->serviceContainer->getParameter('project_dir').'/app/Resources/views/themes/'.$theme_name)){
			    $this->serviceContainer->get('app.app_helper')->setSetting('parameter_theme_name', 'default');
			    return $this->template_exists($filename);
		    }
	    }
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