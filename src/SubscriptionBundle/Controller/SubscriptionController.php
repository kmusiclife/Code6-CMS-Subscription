<?php

namespace SubscriptionBundle\Controller;

use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use AppBundle\Form\Type\PasswordFormType;

/**
 * Subscription controller.
 */
class SubscriptionController extends Controller
{
    /**
     * @Route("/subscription", name="subscription_index")
     * @Method("GET")
     */
    public function indexAction()
    {
		$user = $this->getUser();
		
		if( $user->getStripeSubscriptionId() ){
		
            try{
                $this->get('subscription.stripe_helper')->setApiKey();
                // $customer = \Stripe\Customer::retrieve( $user->getStripeCustomerId() );
                $subscription = \Stripe\Subscription::retrieve( $user->getStripeSubscriptionId() );
            } catch (Exception $e) {
                throw new Exception('Stripe Plan::retrieve Error');
            }

        } else {
            $subscription = null;
        }
        return $this->render('SubscriptionBundle:Subscription:index.html.twig', array(
        	'subscription' => $subscription,
        ));
    }
    /**
     * @Route("/subscription/card", name="subscription_card")
     * @Method("GET")
     */
    public function cardAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        
        $form = $this->createForm('SubscriptionBundle\Form\Type\CardFormType', $user);
        $form->handleRequest($request);
        
        if ($form->isSubmitted()) {

            if ($form->isValid()) {
				
				try{
					
					$stripe_token_id = $request->get('card_form')['stripe_token_id'];
					$customer_id = $user->getStripeCustomerId();
					
					$this->get('subscription.stripe_helper')->setApiKey();
					
					$customer = \Stripe\Customer::retrieve( $customer_id );
					$customer->description = "Update Card Information at ".date('Y-m-d H:i:s');
					$customer->source = $stripe_token_id;
					$customer->save();
					
				} catch (Exception $e) {
					throw new Exception('登録解除中にエラーが発生しました。管理者にご連絡ください。');
				}
				
				$this->addFlash('notice', 'message.subscription.card.update');
				return new RedirectResponse($this->generateUrl('subscription_card'));
				
            }
        }

        return $this->render('SubscriptionBundle:Subscription:card.html.twig', array(
            'user' => $user,
            'form' => $form->createView(),
        ));
    }
    /**
     * @Route("/subscription/invoice", name="subscription_invoice")
     * @Method("GET")
     */
    public function invoiceAction()
    {
		$user = $this->getUser();
        
        if( $user->getStripeSubscriptionId() ){
            try{
                $this->get('subscription.stripe_helper')->setApiKey();
                $invoices = \Stripe\Invoice::all(array(
                    "customer" => $user->getStripeCustomerId(),
                ) );
                
            } catch (Exception $e) {
                throw new Exception('');
            }
        } else $invoices = null;

        return $this->render('SubscriptionBundle:Subscription:invoice.html.twig', array(
	        'invoices' => $invoices
        ));
    }
    /**
     * @Route("/subscription/card/completed", name="subscription_card_completed")
     * @Method({"GET"})
     */
    public function cardCompletedAction()
    {
        return $this->render('SubscriptionBundle:Subscription:card.completed.html.twig', array(
        ));
    }
    /**
     * @Route("/super/stripe/reset/execute", name="super_stripe_reset_execute")
     * @Method("GET")
     */
    public function stripeResetExecuteAction()
    {
        $this->get('app.app_helper')->setSetting('stripe_livemode', null);
        $this->get('app.app_helper')->setSetting('stripe_public_token', null);
        $this->get('app.app_helper')->setSetting('stripe_secret_token', null);
        $this->get('app.app_helper')->setSetting('stripe_access_token', null);

        return $this->redirectToRoute('super_index');

    }
    /**
     * @Route("/super/stripe/reset", name="super_stripe_reset")
     * @Method("GET")
     */
    public function stripeResetAction()
    {
        return $this->render('SubscriptionBundle:Super:stripe.reset.html.twig', array(
        ));
    }
}
