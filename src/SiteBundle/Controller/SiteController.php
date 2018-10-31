<?php

namespace SiteBundle\Controller;

use AppBundle\Entity\User;
use CmsBundle\Entity\Page;
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
	    
        return $this->render('@SiteBundle/Resources/views/index.html.twig', array());
    }
    /**
     * @Route("page/{slug}", name="page_show")
     * @ParamConverter("Page", class="CmsBundle:Page", options={"mapping":{"slug"="slug"}})
     * @Method("GET")
     */
    public function pageShowAction(Page $page)
    {
	    
        return $this->render('@SiteBundle/Resources/views/page.html.twig', array(
            'page' => $page,
        ));
    }
	
    /**
     * @Route("article/{id}", name="article_show")
     * @Method("GET")
     */
    public function articleShowAction(Article $article, Request $request)
    {
	    
        return $this->render('@SiteBundle/Resources/views/article.html.twig', array(
            'article' => $article,
        ));
    }
	
    /**
     * @Route("upload/image/{id}", name="image_show")
     * @Method("GET")
     */
    public function imageShowAction(Image $image)
    {
        return $this->render('@SiteBundle/Resources/views/image.html.twig', array(
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

}
