<?php

namespace SiteBundle\Controller;

use CmsBundle\Entity\Page;
use CmsBundle\Entity\Article;
use CmsBundle\Entity\Image;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

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


}
