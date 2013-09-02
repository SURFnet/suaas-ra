<?php

namespace SURFnet\OneLoginBridgeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
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
     * @Route("/login", name="saml_login")
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
     *
     * @throws BadRequestHttpException
     */
    public function consumerAction()
    {
        $samlResponseBody = $this->getRequest()->request->get('SAMLResponse', false);
        if ($samlResponseBody === false) {
            throw new BadRequestHttpException('No "SAMLResponse" found in the request');
        }

        // @todo resolving to identity + token should be done in firewall
        /** @var \SURFnet\SuAAS\DomainBundle\Entity\SAMLIdentity $samlIdentity */
        $samlIdentity = $this->get('suaas.service.saml')->processResponse($samlResponseBody);

        return $this->render(
            'SURFnetOneLoginBridgeBundle:Default:index.html.twig',
            array(
                'identity' => $samlIdentity,
                'target' => $this->get('session')->get('target')
            )
        );
    }
}
