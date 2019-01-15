<?php

namespace App\Security\User;

use App\Security\WebServiceAuthenticator;
use App\Service\Guzzle\Guzzle;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class WebServiceUserProvider
 * @package App\Security\User
 */
class WebServiceUserProvider implements UserProviderInterface
{
    /**
     * @var WebServiceAuthenticator
     */
    private $authenticator;

    /**
     * @var \Symfony\Component\HttpFoundation\Request
     */
    private $request;

    /**
     * @var Guzzle
     */
    private $client;

    /**
     * WebServiceUserProvider constructor.
     *
     * @param RequestStack $requestStack
     * @param Guzzle $guzzle
     * @param WebServiceAuthenticator $authenticator
     */
    public function __construct(RequestStack $requestStack, Guzzle $guzzle, WebServiceAuthenticator $authenticator)
    {
        $this->request = $requestStack->getCurrentRequest();
        $this->client = $guzzle;
        $this->authenticator = $authenticator;
    }

    /**
     * @param string $username
     *
     * @return UserInterface
     */
    public function loadUserByUsername($username): UserInterface
    {
        $refreshToken = $this->request->cookies->get('REFRESH_TOKEN');

        if ($refreshToken) {
            $refreshToken = substr(base64_decode($refreshToken), 0, -strlen(getenv('APP_SECRET')));

            $response = $this->authenticator->authenticateByRefreshToken($username, $refreshToken);
            if ($response instanceof WebServiceUser) {
                return $response;
            }
        }

        throw new UsernameNotFoundException();
    }

    /**
     * @param UserInterface $user
     *
     * @return UserInterface
     */
    public function refreshUser(UserInterface $user): UserInterface
    {
        if (!$user instanceof WebServiceUser) {
            throw new UnsupportedUserException(
                sprintf('Instances of "%s" are not supported.', get_class($user))
            );
        }

        return $user;
    }

    /**
     * @param string $class
     *
     * @return bool
     */
    public function supportsClass($class): bool
    {
        return WebServiceUser::class === $class;
    }
}