<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Public Controller
 * 
 * @Route("/")
 */
class PublicController extends Controller
{
    /**
     * @Route("/canceled", name="public_canceled")
     * @Method({"GET"})
     */
    public function canceledAction()
    {
        return $this->render('AppBundle:Public:canceled.html.twig', array(
        ));
    }
}
