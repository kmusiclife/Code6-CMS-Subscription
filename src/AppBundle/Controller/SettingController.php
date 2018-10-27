<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Setting;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

use AppBundle\Form\Type\PasswordFormType;

/**
 * @Route("/")
 */
class SettingController extends Controller
{

    /**
     * @Route("setting/{slug}/config", name="setting_config", requirements={"slug"="register_email_subject|register_email|cancel_email_subject|cancel_email"})
     * @Method({"GET", "POST"})
     */
    public function configAction(Request $request)
    {
        $setting = new Setting();
        $setting->setSlug( $request->get('slug') );
        
        $form = $this->createForm('AppBundle\Form\Type\SettingRequireFormType', $setting);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($setting);
            $em->flush();
            return $this->redirectToRoute('fos_user_registration_register');
        }
		
        return $this->render('@AppBundle/Resources/views/Setting/config.html.twig', array(
            'setting' => $setting,
            'form' => $form->createView(),
        ));
    }
	
    /**
     * @Route("admin/setting/new", name="admin_setting_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $setting = new Setting();
        $form = $this->createForm('AppBundle\Form\Type\SettingFormType', $setting);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($setting);
            $em->flush();

            return $this->redirectToRoute('admin_setting_edit', array('id' => $setting->getId()));
        }

        return $this->render('@AppBundle/Resources/views/Setting/new.html.twig', array(
            'setting' => $setting,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("admin/setting/{id}/edit", name="admin_setting_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Setting $setting)
    {
        $deleteForm = $this->createDeleteForm($setting);
        
        $editForm = $this->createForm('AppBundle\Form\Type\SettingFormType', $setting);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin_setting_edit', array('id' => $setting->getId()));
        }

        return $this->render('@AppBundle/Resources/views/Setting/edit.html.twig', array(
            'setting' => $setting,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * @Route("admin/setting/{id}", name="admin_setting_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Setting $setting)
    {
        $form = $this->createDeleteForm($setting);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($setting);
            $em->flush();
        }

        return $this->redirectToRoute('admin_setting_index');
    }

    private function createDeleteForm(Setting $setting)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('admin_setting_delete', array('id' => $setting->getId())))
            ->setMethod('DELETE')
            ->add('password', PasswordFormType::class)
            ->getForm()
        ;
    }
}
