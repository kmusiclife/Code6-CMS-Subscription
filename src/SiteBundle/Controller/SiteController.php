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
 * Article controller.
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
        $em = $this->getDoctrine()->getManager();
        
        return $this->render('index.html.twig', array(
        ));
        
    }
    /**
     * @Route("/page/{slug}", name="page_show")
     * @ParamConverter("Page", class="CmsBundle:Page", options={"mapping":{"slug"="slug"}})
     * @Method("GET")
     */
    public function pageShowAction(Page $page)
    {
        return $this->render('page.html.twig', array(
            'page' => $page,
        ));
    }
	
    /**
     * @Route("/article/{id}", name="article_show")
     * @Method("GET")
     */
    public function articleShowAction(Article $article, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
		$user = $this->getUser();

        return $this->render('article.html.twig', array(
            'article' => $article,
            'user' => $user,
        ));
    }
	
    /**
     * @Route("/upload/image/{id}", name="image_show")
     * @Method("GET")
     */
    public function imageShowAction(Image $image)
    {
        return $this->render('image.html.twig', array(
            'image' => $image
        ));
        
    }
    /**
     * @Route("/site/config", name="site_config")
     * @Method("GET,POST")
     */
    public function configAction(Request $request)
    {
		if( $this->get('app.app_helper')->hasAdmin() > 0 ){
			return new RedirectResponse($this->generateUrl('site_index'));
		}

        $em = $this->getDoctrine()->getManager();

        $user = new User();
        $form = $this->createForm('SiteBundle\Form\Type\ConfigFormType', $user, array(
	        'validation_groups' => array('Profile')
        ));
        $form->handleRequest($request);
		
        if ($form->isSubmitted() && $form->isValid()) {
            
            $em = $this->getDoctrine()->getManager();
            $user->setEnabled(true);
            $user->addRole('ROLE_ADMIN');
            $em->persist($user);
            $em->flush();
            
			return $this->render('@SiteBundle/Resources/views/Config/registered.html.twig', array());
			
        }
        return $this->render('@SiteBundle/Resources/views/Config/new.html.twig', array(
            'user' => $user,
            'form' => $form->createView(),
        ));
        
    }
    /**
     * @Route("/_login", name="site_login")
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
