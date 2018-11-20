<?php

namespace SiteBundle\Controller;

use SiteBundle\Entity\Contact;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Form\FormError;
use AppBundle\Form\Type\PasswordFormType;

/**
 * Contact controller.
 *
 * @Route("/")
 */
class ContactController extends Controller
{
    /**
     * @Route("contact/completed", name="contact_completed")
     * @Method({"GET"})
     */
    public function contactCompletedAction()
    {
        return $this->render('SiteBundle:Contact:completed.html.twig', array(
        ));
    }

    /**
     * @Route("contact/", name="contact_index")
     * @Method({"GET", "POST"})
     */
    public function contactIndexAction(Request $request)
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
            
	        $template = $this->get('twig')->load('SiteBundle:Contact:contact.email.txt.twig');
	        
	        $subject = $template->renderBlock('subject', array('contact' => $contact));
	        $textBody = $template->renderBlock('body_text', array('contact' => $contact));
			
	        $this->get('app.app_helper')->sendEmail(
	        	$this->getParameter('mailer_address'), 
	        	$subject, 
	        	$textBody, 
	        	array(), 
	        	true
	        );

            return $this->redirectToRoute('contact_completed');
        }

        return $this->render('SiteBundle:Contact:new.html.twig', array(
            'contact' => $contact,
            'form' => $form->createView(),
        ));
    }


}
