<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Setting;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;

use Symfony\Component\HttpFoundation\RedirectResponse;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

/**
 * Super controller.
 *
 * @Route("/super")
 */
class SuperController extends Controller
{
    /**
     * @Route("/", name="super_index")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {
        return $this->render('@AppBundle/Resources/views/Super/index.html.twig', array(
	        'body' => ''
        ));

    }
    /**
     * @Route("/theme", name="super_theme")
     * @Method("GET,POST")
     */
    public function themeAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $setting = $em->getRepository('AppBundle:Setting')->findOneBySlug('parameter_theme_name');
        
        $theme_name = $this->get('app.app_helper')->getSetting('parameter_theme_name');
        $setting->setValue($theme_name);
        $setting->setSlug('parameter_theme_name');
        $form = $this->createForm('AppBundle\Form\Type\SettingThemeFormType', $setting);
        
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($setting);
            $em->flush();
            return $this->redirectToRoute('super_index');
        }
        
        return $this->render('@AppBundle/Resources/views/Super/theme.html.twig', array(
            'form' => $form->createView(),
	        'body' => ''
        ));

    }
    /**
     * @Route("/demo", name="super_demo")
     * @Method("GET,POST")
     */
    public function demoAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $setting = $em->getRepository('AppBundle:Setting')->findOneBySlug('parameter_demo_mode');
        if(!$setting) $setting = new Setting();
        $form = $this->createForm('AppBundle\Form\Type\SettingFormType', $setting);

        $form->add('value', ChoiceType::class, array(
            'choices'  => array('デモモード' => 'true', '公開モード' => 'false'),
        ));
        $form->add('slug', HiddenType::class, array('data' => 'parameter_demo_mode'));

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($setting);
            $em->flush();
            return $this->redirectToRoute('super_index');
        }
        
        return $this->render('@AppBundle/Resources/views/Super/demo.html.twig', array(
            'form' => $form->createView(),
	        'body' => ''
        ));

    }
    /**
     * @Route("/members", name="super_members")
     * @Method("GET,POST")
     */
    public function membersAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $setting = $em->getRepository('AppBundle:Setting')->findOneBySlug('parameter_members_mode');
        if(!$setting) $setting = new Setting();
        $form = $this->createForm('AppBundle\Form\Type\SettingFormType', $setting);

        $form->add('value', ChoiceType::class, array(
            'choices'  => array('会員登録サイト' => 'true', '通常サイト' => 'false'),
        ));
        $form->add('slug', HiddenType::class, array('data' => 'parameter_members_mode'));

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($setting);
            $em->flush();
            return $this->redirectToRoute('super_index');
        }
        
        return $this->render('@AppBundle/Resources/views/Super/members.html.twig', array(
            'form' => $form->createView(),
	        'body' => ''
        ));

    }
}
