<?php

/**
 * This file is part of the authbucket/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AuthBucket\OAuth2\Security\Firewall;

use AuthBucket\OAuth2\Exception\InvalidRequestException;
use AuthBucket\OAuth2\Security\Authentication\Token\ClientToken;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Http\Firewall\ListenerInterface;

/**
 * TokenListener implements OAuth2 token endpoint authentication.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class TokenListener implements ListenerInterface
{
    protected $securityContext;
    protected $authenticationManager;
    protected $providerKey;

    public function __construct(
        SecurityContextInterface $securityContext,
        AuthenticationManagerInterface $authenticationManager,
        $providerKey
    )
    {
        $this->securityContext = $securityContext;
        $this->authenticationManager = $authenticationManager;
        $this->providerKey = $providerKey;
    }

    public function handle(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        // At least one (and only one) of client credentials method required.
        if (!$request->headers->get('PHP_AUTH_USER', false) && !$request->request->get('client_id', false)) {
            throw new InvalidRequestException(array(
                'error_description' => 'The request is missing a required parameter',
            ));
        } elseif ($request->headers->get('PHP_AUTH_USER', false) && $request->request->get('client_id', false)) {
            throw new InvalidRequestException(array(
                'error_description' => 'The request utilizes more than one mechanism for authenticating the client',
            ));
        }

        // Check with HTTP basic auth if exists.
        if ($request->headers->get('PHP_AUTH_USER', false)) {
            $clientId = $request->headers->get('PHP_AUTH_USER', false);
            $clientSecret = $request->headers->get('PHP_AUTH_PW', false);
        } else {
            $clientId = $request->request->get('client_id', false);
            $clientSecret = $request->request->get('client_secret', false);
        }

        if (null !== $token = $this->securityContext->getToken()) {
            if ($token instanceof ClientToken
                && $token->isAuthenticated()
                && $token->getClientId() === $clientId
            ) {
                return;
            }
        }

        $token = new ClientToken($clientId, $clientSecret, $this->providerKey);
        $tokenAuthenticated = $this->authenticationManager->authenticate($token);
        $this->securityContext->setToken($tokenAuthenticated);
    }
}
