<?php

namespace SURFnet\OneLoginBridgeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DefaultController
 * @package SURFnet\OneLoginBridgeBundle\Controller
 *
 * @Route("/saml")
 *
 * @author Daan van Renterghem <dvrenterghem@ibuildings.nl>
 */
class SamlController extends Controller
{
    /**
     * @Route("/login")
     *
     * @return Response
     */
    public function loginAction()
    {
        /** @var \OneLogin_Saml_AuthRequest $samlRequest */
        $samlRequest = $this->get('surfnet.saml.request');
        return $this->redirect($samlRequest->getRedirectUrl());
    }

    /**
     * @Route("/metadata")
     *
     * @return Response
     */
    public function metadataAction()
    {
        /** @var \OneLogin_Saml_Metadata $metadata */
        $metadata = $this->get('surfnet.saml.metadata');
        return new Response($metadata->getXml());
    }

    /**
     * @Route("/consume", name="saml_consume")
     *
     * @return Response
     */
    public function consumerAction()
    {
        /** @var \SURFnet\OneLoginBridgeBundle\Service\ResponseAdapter $samlResponse */
        $samlResponse = $this->get('surfnet.saml.response');
var_dump($samlResponse->getNameId());
        return $this->render(
            'SURFnetOneLoginBridgeBundle:Default:index.html.twig',
            array('name' => $samlResponse->getSessionExpirationDate()->format('c'))
        );
    }
}
