<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Invitation;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

use Endroid\QrCode\QrCode;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use AppBundle\Form\Type\PasswordFormType;

/**
 * Invitation controller.
 *
 * @Route("/")
 */
class InvitationController extends Controller
{
    /**
     * @Route("/invitation", name="invitation_index_public")
     * @Method("GET")
     */
    public function invitationAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        
        if( false == $this->get('app.app_helper')->getSetting('parameter_invitation_mode') ){
            $this->get('session')->set('invitation_passed', false);
            return $this->redirectToRoute('fos_user_registration_register');
        }
        $code = $request->request->get('code');
        
        if($code){

            if( true == $this->get('session')->get('invitation_passed') ) {
                return $this->redirectToRoute('fos_user_registration_register');
            }    
            if( false == $this->get('app.app_helper')->recaptchaCheck() ){
                $this->get('session')->getFlashBag()->add('error', 'スパム対策によるブロック、またはタイムアウトのため再度入力してください');
                return $this->redirectToRoute('invitation_index_public');
            }
            $invitation = $em->getRepository('AppBundle:Invitation')->findOneBy(array('code' => $code));
            if( null == $invitation ){
                $this->get('session')->getFlashBag()->add('error', '招待コードが正しくありません正しい招待コードを入力してください');
                return $this->redirectToRoute('invitation_index_public');
            }
            if($invitation->getCountCurrent() < $invitation->getCountLimit())
            {
                $this->get('session')->set('invitation_passed', $invitation->getCode());
                return $this->redirectToRoute('fos_user_registration_register');
            } else {
                $this->get('session')->getFlashBag()->add('error', '招待コードの上限を超えましたこの招待コードは使えません');
                return $this->redirectToRoute('invitation_index_public');
            }

        } else {
            return $this->render('@AppBundle/Resources/views/Invitation/invitation.html.twig', array());
        }
    }
    
    /**
     * @Route("/invitation/code/{code}", name="invitation_index_public_code")
     * @Method("GET")
     */
    public function invitationCodeAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $code = $request->get('code');
        $invitation = $em->getRepository('AppBundle:Invitation')->findOneBy(array('code' => $code));
        
        if(null == $invitation){
            $this->get('session')->set('invitation_passed', null);
            return new RedirectResponse( $this->generateUrl('invitation_index_public') );
        }

        if( $this->get('app.app_helper')->getSetting('parameter_invitation_mode') == "true" ){
            
			if($invitation->getCountCurrent() >= $invitation->getCountLimit())
			{
				$this->get('session')->getFlashBag()->add('error', '招待コードの上限を超えました。この招待コードは使えません。');
				return new RedirectResponse( $this->generateUrl('invitation_index_public') );
            } else {
                $this->get('session')->set('invitation_passed', $invitation->getCode());
            }
            return $this->render('@AppBundle/Resources/views/Invitation/invitation.code.html.twig', array(
                'invitation' => $invitation
            ));

        } else {
            return $this->redirectToRoute('fos_user_registration_register');
        }

    }
    /**
     * @Route("/admin/invitation/qrcode/{id}", name="invitation_qrcode")
     * @Method("GET")
     */
    public function showQRCode(Invitation $invitation)
    {
        /* https://github.com/endroid/qr-code */
        $qrCode = new QrCode( $this->generateUrl('invitation_index_public_code', array('code' => $invitation->getCode()), UrlGeneratorInterface::ABSOLUTE_URL) );
        $qrCode->setSize(500);
        $qrCode->setWriterByName('png');
        $qrCode->setMargin(10);
        $qrCode->setEncoding('UTF-8');
        $headers = array('Content-Type' => $qrCode->getContentType());
        return new Response($qrCode->writeString(), 200, $headers);
    }
    /**
     * @Route("/admin/invitation", name="invitation_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $pager = $this->get('app.app_pager');
        $pager->setInc(10);
        $pager->setPath('article_index'); 
        
        $invitations = $pager->getRepository( 'AppBundle:Invitation', array(), array('id' => 'DESC') );

        return $this->render('@AppBundle/Resources/views/Invitation/index.html.twig', array(
            'pager' => $pager,
            'invitations' => $invitations,
        ));
    }

    /**
     * @Route("/admin/invitation/new", name="invitation_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $invitation = new Invitation();
        $form = $this->createForm('AppBundle\Form\Type\InvitationType', $invitation);
        $invitation->setCode( sha1(random_int(PHP_INT_MIN, PHP_INT_MAX)).uniqid() );
        $invitation->setEnabled(true);
        $invitation->setCreatedUser($this->getUser());

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($invitation);
            $em->flush();

            $this->get('session')->getFlashBag()->add('notice', '招待を追加しました');
            return $this->redirectToRoute('invitation_index');
        }

        return $this->render('@AppBundle/Resources/views/Invitation/new.html.twig', array(
            'invitation' => $invitation,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/admin/invitation/{id}/edit", name="invitation_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Invitation $invitation)
    {
        $qb = $this->getDoctrine()->getManager()->createQueryBuilder();
        if(!$invitation->getCreatedUser()){
            $invitation->setCreatedUser($this->getUser());
        }
        $deleteForm = $this->createDeleteForm($invitation);
        $editForm = $this->createForm('AppBundle\Form\Type\InvitationType', $invitation);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->get('session')->getFlashBag()->add('notice', '招待を編集しました');
            return $this->redirectToRoute('invitation_index');
        }

	    $qb->select('count(u)')
	        ->from('AppBundle:User', 'u')
            ->where('u.invitation = :id')
            ->setParameter('id', $invitation->getId());
        
        $invitation_count = (int)$qb->getQuery()->getSingleScalarResult();

        if($invitation_count > 0) {
            $is_delete = false;
        } else $is_delete = true;

        return $this->render('@AppBundle/Resources/views/Invitation/edit.html.twig', array(
            'invitation' => $invitation,
            'edit_form' => $editForm->createView(),
            'is_delete' => $is_delete,
            'invitation_count' => $invitation_count,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * @Route("/admin/invitation/{id}", name="invitation_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Invitation $invitation)
    {
        $form = $this->createDeleteForm($invitation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($invitation);
            $em->flush();
        }

        return $this->redirectToRoute('invitation_index');
    }

    /**
     * @param Invitation $invitation The invitation entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Invitation $invitation)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('invitation_delete', array('id' => $invitation->getId())))
            ->setMethod('DELETE')
            ->add('password', PasswordFormType::class)
            ->getForm()
        ;
    }
}
