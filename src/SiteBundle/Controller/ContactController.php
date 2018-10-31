<?php

namespace SiteBundle\Controller;

use SiteBundle\Entity\Contact;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("contact")
 */
class ContactController extends Controller
{

    /**
     * @Route("/completed", name="contact_completed")
     * @Method({"GET"})
     */
    public function completedAction()
    {
        return $this->render('@SiteBundle/Resources/views/contact.completed.html.twig', array(
        ));
    }

    /**
     * @Route("/", name="contact_index")
     * @Method({"GET", "POST"})
     */
    public function indexAction(Request $request)
    {
        $contact = new Contact();
        $form = $this->createForm('SiteBundle\Form\Type\ContactFormType', $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($contact);
            $em->flush();

            return $this->redirectToRoute('contact_completed');
        }

        return $this->render('@SiteBundle/Resources/views/contact.html.twig', array(
            'contact' => $contact,
            'form' => $form->createView(),
        ));
    }

}
