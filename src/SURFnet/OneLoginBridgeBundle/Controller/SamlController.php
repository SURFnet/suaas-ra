<?php

namespace SURFnet\OneLoginBridgeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DefaultController
 * @package SURFnet\OneLoginBridgeBundle\Controller
 *
 * @Route("/saml")
 *
 * @author Daan van Renterghem <dvrenterghem@gmail.com>
 */
class SamlController extends Controller
{
    /**
     * @Route("/login")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function loginAction()
    {
        /** @var SURFnet\OneLoginBridgeBundle\Service\Configuration $conf */
        $settings = $this->get('surfnet.onelogin_bridge.settings');
        return $this->render('SURFnetOneLoginBridgeBundle:Default:index.html.twig', array('name' => $settings->spIssuer));
    }

    /**
     * @Route("/metadata")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function metadataAction()
    {
        return $this->render('SURFnetOneLoginBridgeBundle:Default:index.html.twig', array('name' => 'Daan'));
    }

    /**
     * @Route("/consumer")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function consumerAction()
    {
        return $this->render('SURFnetOneLoginBridgeBundle:Default:index.html.twig', array('name' => 'Daan'));
    }
}
