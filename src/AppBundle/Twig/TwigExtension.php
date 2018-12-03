<?php

namespace AppBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use AppBundle\Entity\User;

class TwigExtension extends AbstractExtension
{
    protected $serviceContainer;
    protected $requestStack;
    protected $entityManager;
    protected $router;
	protected $tokenStorage;
	
    protected $pager;

    public function __construct(
    	ContainerInterface $serviceContainer, 
    	RequestStack $requestStack,
        EntityManagerInterface $entityManager,
		UrlGeneratorInterface $router,
		TokenStorageInterface $tokenStorage
    )
    {
        $this->serviceContainer = $serviceContainer;
        $this->requestStack = $requestStack;
        $this->EntityManager = $entityManager;
		$this->router = $router;
		$this->tokenStorage = $tokenStorage;
		
    }
    public function getFilters()
    {
        return array(
            new TwigFilter('upload_uri', array($this, 'upload_uri')),
            new TwigFilter('absolute_url', array($this, 'absolute_url')),
            new TwigFilter('autop', array($this, 'autop')),        
        );
	}
	public function autop($plain_text)
	{
		$splited_text = preg_split("/\R\R+/", $plain_text, -1, PREG_SPLIT_NO_EMPTY);
		$result = null;
		foreach($splited_text as $paragraph){
			$result .= "<p>" . $paragraph . "</p>\n";
		}
		return $result;
	}
    public function absolute_url($src)
    {
	    $request = $this->requestStack->getCurrentRequest();
	    return $request->getScheme() . '://' . $request->getHttpHost() . $request->getBaseURL(). '/' .$src;
    }
    public function upload_uri($src)
    {
		return $this->serviceContainer->getParameter('upload_uri').'/'.$src;
    }
	public function getFunctions()
	{
	    return array(
	        new \Twig_SimpleFunction('is_home', array($this, 'is_home')),
	        new \Twig_SimpleFunction('is_article', array($this, 'is_article')),
			new \Twig_SimpleFunction('this_permalink', array($this, 'this_permalink')),
			new \Twig_SimpleFunction('get_template_directory_uri', array($this, 'get_template_directory_uri')),
	        new \Twig_SimpleFunction('get_template_url', array($this, 'get_template_directory_uri')),
			
			new \Twig_SimpleFunction('get_admin_template_directory_uri', array($this, 'get_admin_template_directory_uri')),
	        new \Twig_SimpleFunction('get_admin_template_url', array($this, 'get_admin_template_directory_uri')),

			new \Twig_SimpleFunction('template_exists', array($this, 'template_exists')),
			new \Twig_SimpleFunction('template_path', array($this, 'template_path')),
			new \Twig_SimpleFunction('admin_template_path', array($this, 'admin_template_path')),
			new \Twig_SimpleFunction('template_layout', array($this, 'template_layout')),
			new \Twig_SimpleFunction('admin_template_layout', array($this, 'admin_template_layout')),

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

			new \Twig_SimpleFunction('article_date', array($this, 'article_date')),
			new \Twig_SimpleFunction('article_permalink', array($this, 'article_permalink')),
	        new \Twig_SimpleFunction('article_image', array($this, 'article_image')),
			new \Twig_SimpleFunction('article_body', array($this, 'article_body')),

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
	public function admin_template_layout()
	{
		$template_file = $this->admin_template_path("layout.html.twig");
		if(null == $template_file) return $this->admin_template_path("layout.html.twig", "default");
		return $template_file;
	}
	public function template_path($filename, $theme_name=null)
	{	
		$theme_dir = $this->serviceContainer->getParameter('project_dir').'/app/Resources/views/themes/';
		if(null == $theme_name) $theme_name = $this->serviceContainer->get('app.app_helper')->theme_name();
		$template_dir = $theme_dir.$theme_name;
		$template_file = $template_dir.'/'.$filename;

		if( file_exists($template_file) ) return $template_file;
		return $theme_dir.'default'.'/'.$filename;
	}
	public function admin_template_path($filename, $theme_name=null)
	{
		$admin_theme_dir = $this->serviceContainer->getParameter('project_dir').'/src/AppBundle/Resources/views/Template/';

		if(null == $theme_name){
			$theme_name = $this->serviceContainer->get('app.app_helper')->getSetting('parameter_admin_theme_name');
		}
		if(null == $theme_name){
			$theme_name = $this->serviceContainer->get('app.app_helper')->setSetting('parameter_admin_theme_name', 'default');
		}
		$template_dir = $admin_theme_dir.$theme_name;
		$template_file = $template_dir.'/'.$filename;
		
		if(null == file_exists($template_dir)){
			$theme_name = $this->serviceContainer->get('app.app_helper')->setSetting('parameter_admin_theme_name', 'default');
			$template_file = $admin_theme_dir.$theme_name.'/'.$filename;
		}

		if( file_exists($template_file) ) return $template_file;
		return false;
	}

	public function get_article_embed()
	{
		$theme_dir = $this->serviceContainer->getParameter('project_dir').'/app/Resources/views/themes/';
		$theme_name = $this->serviceContainer->get('app.app_helper')->theme_name();
		
	    if( $this->template_exists('_cms/article.index.embed.html.twig') ){
		    $template_file = $theme_dir.$theme_name.'/_cms/article.index.embed.html.twig';
	    } else {
			$template_file = $theme_dir.'default/_cms/article.index.embed.html.twig';
	    }
	    return $template_file;
	}
	public function article_body($article, $params=array())
	{
		$image_format = isset($params['image_format']) ? $params['image_format'] : 'image_normal';
		$image_class = isset($params['image_class']) ? $params['image_class'] : 'code6-image';
		$image_style = isset($params['image_style']) ? $params['image_style'] : '';
		$disable_autop = isset($params['disable_autop']) ? $params['disable_autop'] : true;
		$body = $article->getBody();
		$inc = 1;

		if( $this->template_exists('_cms/article.image.embed.html.twig') ){
			$template = $this->serviceContainer->get('twig')->load($this->template_path('_cms/article.image.embed.html.twig'));
		} else {
			$template = $this->serviceContainer->get('twig')->load($this->template_path('_cms/article.image.embed.html.twig', "default"));
		}
		
		foreach($article->getImages() as $image)
		{
			$_image = $this->serviceContainer->getParameter('upload_uri').'/'.$image->getSrc();

			$_params = array(
				'src' 		=> $this->serviceContainer->get('liip_imagine.cache.manager')->getBrowserPath($_image, $image_format), 
				'class' 	=> $image_class ? $image_class : '', 
				'id' 		=> 'code6-cms-image-'.$image->getId(), 
				'alt' 		=> htmlspecialchars($image->getTitle()),
				'style' 	=> $image_style ? $image_style : ''
			);
			
			$image_tag = $template->render($_params);
			$reg = '\[image'.$inc.'\]';
			$body = preg_replace('/'.$reg.'/', $image_tag, $body);
			$inc ++;
		}
		if(true == $disable_autop) return $this->autop($body);

		return $body;
	}
	public function article_date($article, $date_format='Y-m-d')
	{
		return $article->getPublishedAt()->format($date_format);
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
	public function have_new_articles($date_diff=90)
	{
		$article = $this->EntityManager->getRepository('CmsBundle:Article')->findOneBy(array(), array('createdAt' => 'DESC'));
		if($article){
			$current_date = new \DateTime("now");
			$interval = $current_date->diff( $article->getPublishedAt() );
			if( (int)$interval->format('%a') < $date_diff ) return true;
		}
		return false;
	}
	public function get_articles($limit=5)
	{
        $pager = $this->serviceContainer->get('app.app_pager');
        $pager->setInc($limit);
        $pager->setPath('article_index_public'); 
		$articles = $pager->getArticles();
		$this->pager = $pager;
		
		return $articles;
	}
	public function get_pager()
	{
		$user = $this->tokenStorage->getToken()->getUser();
		$theme_name = $this->serviceContainer->get('app.app_helper')->theme_name();
		$theme_dir = $this->serviceContainer->getParameter('project_dir').'/app/Resources/views/themes/';

		if( $this->template_exists('_cms/pager.html.twig') ){
		    $template_file = $theme_dir.$theme_name.'/_cms/pager.html.twig';
	    } else {
			$template_file = $theme_dir.'default/_cms/pager.html.twig';
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
		$theme_dir = $this->serviceContainer->getParameter('project_dir').'/app/Resources/views/themes/';
		$theme_name = $this->serviceContainer->get('app.app_helper')->theme_name();
		$template_file = $theme_dir.$theme_name.'/'.$filename.($extname ? '-'.$extname : '').'.html.twig';
	    
	    return $template_file;
	}
	public function template_exists($filename)
	{
		$theme_dir = $this->serviceContainer->getParameter('project_dir').'/app/Resources/views/themes/';
		$theme_name = $this->serviceContainer->get('app.app_helper')->theme_name();
		$template_file = $theme_dir.$theme_name.'/'.$filename;

	    $exists = file_exists($template_file);
	    if(false == $exists){
		    if(false == file_exists($theme_dir.$theme_name)){
				$this->serviceContainer->get('app.app_helper')->setSetting('parameter_theme_name', 'default');
				$this->serviceContainer->get('app.app_helper')->updateSettingCache('parameter_theme_name', 'default');
			    return $this->template_exists($filename);
		    }
	    }
	    return file_exists($template_file);
	}
	public function get_admin_template_directory_uri()
	{
		if( $this->serviceContainer->get('app.app_helper')->getSetting('parameter_admin_theme_name') ){
			$uri = 'admin/'.$this->serviceContainer->get('app.app_helper')->getSetting('parameter_admin_theme_name');
			return $this->absolute_url($uri);
		}
		return '/';
	}
	public function get_template_directory_uri()
	{
		$theme_name = $this->serviceContainer->get('app.app_helper')->theme_name();
		if( $theme_name ){
			$uri = 'themes/'.$theme_name;
			return $this->absolute_url($uri);
		}
		return '/';
	}
	public function this_permalink($app, $absolutely=false)
	{
		if($absolutely) return $app->getRequest()->getUri();
		return $app->getRequest()->getRequestUri();
	}
	public function is_home($app)
	{
		return $app->getRequest()->getRequestUri() == '/' ? true : false;
	}
	public function is_article($app)
	{
		return preg_match('/\/article$|^\/article\/(.*)$/', $app->getRequest()->getRequestUri());
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