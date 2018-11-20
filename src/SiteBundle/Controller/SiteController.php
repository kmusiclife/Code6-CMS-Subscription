<?php

namespace SiteBundle\Controller;

use AppBundle\Entity\User;
use CmsBundle\Entity\Article;
use CmsBundle\Entity\Image;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\RedirectResponse;

use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

/**
 * Site controller.
 *
 * @Route("/")
 */
class SiteController extends Controller
{

    /**
     * @Route("/", name="site_index")
     * @Method("GET")
     */
    public function indexAction()
    {
	    $response = $this->get('app.init_helper')->initSite();
	    if(null != $response) return $response;
	    
        return $this->render('SiteBundle:Index:show.html.twig', array());
    }
    /**
     * @Route("article/{slug}", name="article_show")
     * @ParamConverter("Article", class="CmsBundle:Article", options={"mapping":{"slug"="slug"}})
     * @Method("GET")
     */
    public function articleShowAction(Article $article, Request $request)
    {
	    
        return $this->render('SiteBundle:Article:show.html.twig', array(
            'article' => $article,
        ));
    }
	
    /**
     * @Route("upload/image/{id}", name="image_show")
     * @Method("GET")
     */
    public function imageShowAction(Image $image)
    {
        return $this->render('SiteBundle:Image:show.html.twig', array(
            'image' => $image,
        ));
        
    }
    /**
     * @Route("_login", name="site_login")
     * @Method("GET")
     */
    public function afterLogin(AuthorizationCheckerInterface $authChecker)
    {
	    if($authChecker->isGranted('ROLE_ADMIN'))
	    	return new RedirectResponse($this->generateUrl('admin_index'));
	    else
	    	return new RedirectResponse($this->generateUrl('site_index'));
    }
    /**
     * @Route("/{slug}", name="site_static")
     * @Method("GET")
     */
    public function staticAction($slug)
    {
	    $theme_name = $this->get('app.app_helper')->getSetting('parameter_theme_name');
	    $template_file = $this->getParameter('project_dir').'/app/Resources/views/themes/'.$theme_name.'/_static/'.$slug.'.html.twig';
	    if( false == file_exists($template_file) ){
		    throw new NotFoundHttpException("Page not found");
	    }
        return $this->render(
        	'SiteBundle:Static:show.html.twig', 
        	array('slug' => $slug)
        );
    }
    	
}
