<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use AppBundle\Form\Type\PasswordFormType;
use Symfony\Component\Config\Definition\Exception\Exception;

/**
 * Cancel controller.
 *
 * @Route("cancel")
 */
class CancelController extends Controller
{
    /**
     * @Route("/", name="cancel_index")
     * @Method("GET")
     */
    public function indexAction()
    {
		$user = $this->getUser();
       return $this->render('@AppBundle/Resources/views/Cancel/index.html.twig', array(
	        'user' => $user
        ));
    }
    /**
     * @Route("/confirm", name="cancel_confirm")
     * @Method("GET")
     */
    public function cancelAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();
		$user = $this->getUser();

        $form = $this->createForm('AppBundle\Form\Type\PasswordFormType', $user);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            
            if ($form->isValid()) {
				
				try{

					$this->get('subscription.stripe_helper')->setApiKey();

					$subscription = \Stripe\Subscription::retrieve( $user->getStripeSubscriptionId() );
					$subscription->cancel();

					$userManager = $this->container->get('fos_user.user_manager');
					$userManager->deleteUser($user);
					
				} catch (Exception $e) {
					throw new Exception('登録解除中にエラーが発生しました。管理者にご連絡ください。');
				}

		        $template = $this->get('twig')->load('AppBundle:Cancel:cancel.email.txt.twig');
		        
		        $subject = $template->renderBlock('subject', array('user' => $user));
		        $textBody = $template->renderBlock('body_text', array('user' => $user));
				
		        $this->get('app.app_helper')->sendEmail(
		        	$user->getEmail(), 
		        	$subject, $textBody, 
		        	array(), false
		        );
				
                return new RedirectResponse($this->generateUrl('cancel_completed'));
            }
        }

        return $this->render('@AppBundle/Resources/views/Cancel/confirm.html.twig', array(
            'user' => $user,
            'form' => $form->createView(),
        ));
		
    }
    /**
     * @Route("/completed", name="cancel_completed")
     * @Method({"GET"})
     */
    public function completedAction()
    {
        return $this->render('AppBundle:Cancel:completed.html.twig', array(
        ));
    }
}
