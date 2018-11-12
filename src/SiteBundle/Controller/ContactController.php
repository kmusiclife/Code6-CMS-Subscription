<?php

namespace SiteBundle\Controller;

use SiteBundle\Entity\Contact;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormError;

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
        return $this->render('SiteBundle:Contact:completed.html.twig', array(
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
        
        if($form->isSubmitted()){
	        
	        $recaptcha_json = $this->get('app.app_helper')->curlRequest(
	        	'https://www.google.com/recaptcha/api/siteverify',
	        	array(
		        	'response' => $contact->getRecaptcha(),
					'secret' => $this->getParameter('recaptcha_secret_key')
				)
			);
	        $recaptcha = json_decode($recaptcha_json);
	        
	        if($recaptcha->success){
	        	$contact->setRecaptcha($recaptcha->success);
	        } else {
	        	$contact->setRecaptcha(null);
	        	$form->get('recaptcha')->addError( new FormError('再度認証を行ってください。') );
	        }
        }
        
        if ($form->isSubmitted() && $form->isValid()) {
			
            $em = $this->getDoctrine()->getManager();
            $em->persist($contact);
            $em->flush();

	        $this->get('app.app_helper')->sendEmailBySetting(
	        	$this->getParameter('mailer_address'), 
	        	'contact_email_subject', 
	        	'contact_email', 
	        	array('contact' => $contact),
	        	false
	        );

            return $this->redirectToRoute('contact_completed');
        }

        return $this->render('SiteBundle:Contact:index.html.twig', array(
            'contact' => $contact,
            'form' => $form->createView(),
        ));
    }

}
