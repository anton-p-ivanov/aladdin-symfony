<?php

namespace App\Form\Profile;

use App\Security\User\WebServiceUser;
use App\Security\WebServiceAuthenticator;
use App\Service\Guzzle\Guzzle;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class BaseHandler
 *
 * @package App\Form\Profile
 */
class BaseHandler
{
    /**
     * @var \Symfony\Component\HttpFoundation\Request
     */
    protected $request;

    /**
     * @var Guzzle
     */
    protected $client;

    /**
     * @var WebServiceUser
     */
    protected $user;

    /**
     * @var WebServiceAuthenticator
     */
    private $autenticator;

    /**
     * ResetHandler constructor.
     *
     * @param Guzzle $client
     * @param RequestStack $requestStack
     * @param WebServiceAuthenticator $authenticator
     */
    public function __construct(Guzzle $client, RequestStack $requestStack, WebServiceAuthenticator $authenticator)
    {
        // Storing guzzle client instance
        $this->client = $client;

        // Storing current request for future use
        $this->request = $requestStack->getCurrentRequest();

        // Storing WebServiceAuthenticator instance
        $this->autenticator = $authenticator;
    }

    /**
     * @return WebServiceUser
     */
    public function getUser(): WebServiceUser
    {
        return $this->user;
    }

    /**
     * @param string $username
     * @param string $password
     *
     * @return bool
     */
    protected function auth(string $username, string $password): bool
    {
        $response = $this->autenticator->authenticateByUserCredentials($username, $password);
        if ($response instanceof WebServiceUser) {
            $this->user = $response;

            return true;
        }

        return false;
    }
}