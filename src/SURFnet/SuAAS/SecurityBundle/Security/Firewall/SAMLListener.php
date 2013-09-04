<?php

namespace SURFnet\SuAAS\SecurityBundle\Security\Firewall;

use Monolog\Logger;
use SURFnet\SuAAS\DomainBundle\Service\SAMLService;
use SURFnet\SuAAS\SecurityBundle\Security\Authentication\Token\SAMLToken;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Http\Firewall\ListenerInterface;

/**
 * Class SamlListener
 * @package SURFnet\SuAAS\SecurityBundle\Security\Firewall
 *
 * Listener for the Firewall that uses the SAML Auth provider to authenticate users
 *
 * @author Daan van Renterghem <dvrenterghem@ibuildings.nl>
 */
class SamlListener implements ListenerInterface
{
    /**
     * @var SecurityContextInterface
     */
    private $securityContext;

    /**
     * @var AuthenticationManagerInterface
     */
    private $authenticationManager;

    /**
     * @var SAMLService
     */
    private $samlService;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * Constructor
     *
     * @param SecurityContextInterface $securityContext
     * @param AuthenticationManagerInterface $authenticationManager
     * @param SAMLService $samlService
     * @param Logger $logger
     */
    public function __construct(
        SecurityContextInterface $securityContext,
        AuthenticationManagerInterface $authenticationManager,
        SAMLService $samlService,
        Logger $logger
    ) {
        $this->securityContext = $securityContext;
        $this->authenticationManager = $authenticationManager;
        $this->samlService = $samlService;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        // ensure we have a posted SAML Authn Request
        $samlResponseBody = $request->get('SAMLResponse', false);
        if (!($request->isMethod('POST') && $samlResponseBody !== false)) {
            return;
        }

        $samlIdentity = $this->samlService->processResponse($samlResponseBody);
        $token = new SAMLToken($samlIdentity);

        try {
            $authToken = $this->authenticationManager->authenticate($token);
            $this->securityContext->setToken($authToken);

            return;
        } catch (AuthenticationException $failed) {
            $this->logger->addNotice(
                sprintf(
                    'Authentication failed by exception: %s',
                    $failed->getMessage()
                ),
                array('Full Exception' => serialize($failed))
            );

            // Deny authentication with a '403 Forbidden' HTTP response
            $response = new Response();
            $response->setStatusCode(403);
            $event->setResponse($response);
        }

        // By default deny authorization
        // @todo consider making redirect to "/"
        $response = new Response();
        $response->setStatusCode(403);
        $event->setResponse($response);
    }
}
