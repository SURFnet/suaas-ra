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
     * @Route("/", name="landing")
     * @Template()
     *
     * @return array
     */
    public function indexAction()
    {
        return array();
    }

    /**
     * @Route("/logout")
     * @Template()
     *
     * @return array
     */
    public function logoutAction()
    {
        $this->get('session')->invalidate();

        return $this->redirect($this->generateUrl('landing'));
    }

    /**
     * @Route("/error", name="error")
     * @Template()
     *
     * @return array
     */
    public function errorAction()
    {
        $message = $this->get('session')->get('error_message', false);

        if ($message) {
            return array('message' => $message);
        }

        return array();
    }
}
