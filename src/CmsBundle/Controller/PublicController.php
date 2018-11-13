<?php

namespace CmsBundle\Controller;

use CmsBundle\Entity\Contact;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

use AppBundle\Form\Type\PasswordFormType;

/**
 * Contact controller.
 *
 * @Route("/")
 */
class PublicController extends Controller
{
    /**
     * @Route("contact/completed", name="contact_completed")
     * @Method({"GET"})
     */
    public function contactCompletedAction()
    {
        return $this->render('CmsBundle:Contact:public_completed.html.twig', array(
        ));
    }

    /**
     * @Route("contact/", name="contact_index")
     * @Method({"GET", "POST"})
     */
    public function contactIndexAction(Request $request)
    {
        $contact = new Contact();
        $form = $this->createForm('CmsBundle\Form\Type\ContactFormType', $contact);
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

        return $this->render('CmsBundle:Contact:public_index.html.twig', array(
            'contact' => $contact,
            'form' => $form->createView(),
        ));
    }


}
