<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;

use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Public controller.
 *
 * @Route("/admin")
 */
class AdminController extends Controller
{

    /**
     * @Route("/", name="admin_index")
     * @Method("GET")
     */
    public function indexAction()
    {
	    
        $em = $this->getDoctrine()->getManager();
        return $this->render('@AppBundle/Resources/views/Admin/index.html.twig', array(
	        'body' => ''
        ));

    }

}
