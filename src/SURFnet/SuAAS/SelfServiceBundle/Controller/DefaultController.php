<?php

namespace SURFnet\SuAAS\SelfServiceBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DefaultController
 * @package SURFnet\SuAAS\SelfServiceBundle\Controller
 *
 * @Route("/")
 *
 * @author Daan van Renterghem <dvrenterghem@ibuildings.nl>
 */
class DefaultController extends Controller
{
    /**
     * Catch all for the root of the domain
     *
     * @Route("/")
     */
    public function kickstartAction()
    {
        return $this->redirect($this->generateUrl('landing'));
    }

    /**
     * [!!!] PILOT ONLY
     * Landing Page
     *
     * @Route("/start", name="landing")
     * @Template()
     *
     * @return array
     */
    public function indexAction()
    {
        $session = $this->get('session');
        $targetURL = $session->get('_security.self_registration.target_path', false);
        $targetPath = @parse_url($targetURL, PHP_URL_PATH);
        $targetParams = @parse_url($targetURL, PHP_URL_QUERY);

        if ($targetPath !== false && strpos($targetPath, $this->generateUrl('self_service_registration')) === 0) {
            $this->get('session')->set('target', 'self_service_registration');
            $this->get('session')->set('target_query', $targetParams);

            return $this->redirect($this->generateUrl('saml_login'));
        }

        return array();
    }

    /**
     * [!!!] PILOT ONLY
     * Handles the redirect to the saml login, sets a simple return_url as the
     * SAML one may be ignored by the IdP
     *
     * @Route("/login/{flow}", name="login")
     *
     * @return array
     */
    public function loginAction()
    {
        $type = $this->getRequest()->get('flow', false);
        $session = $this->get('session');

        if ($type === false) {
            $session->set('error_message', 'Cannot determine which flow to start');
            return $this->redirect($this->generateUrl('error'));
        }

        if ($type === 'self') {
            $this->get('session')->set('target', 'self_service_selecttoken');
        } elseif ($type === 'RA') {
            $this->get('session')->set('target', 'management_user_overview');
        } elseif ($type === 'RAC') {
            $this->get('session')->set('target', 'management_ra_overview');
        } else {
            $this->get('session')->set('error_message', 'Cannot determine which flow to start');
            return $this->redirect($this->generateUrl('error'));
        }

        return $this->redirect($this->generateUrl('saml_login'));
    }

    /**
     * Catch all custom error action - just makes life easier than creating a
     * custom error page around the session.flashbag
     *
     * @Route("/error", name="error")
     * @Template()
     *
     * @return array
     */
    public function errorAction()
    {
        $session = $this->get('session');
        $message = $session->get('error_message', false);

        if ($message) {
            $session->remove('error_message');
            return array('message' => $message);
        }

        return array();
    }
}
