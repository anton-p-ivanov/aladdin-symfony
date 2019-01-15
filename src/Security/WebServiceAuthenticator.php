<?php

namespace App\Security;

use App\Security\User\WebServiceUser;
use App\Service\Guzzle\Guzzle;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;

/**
 * Class WebServiceAuthenticator
 * @package App\Security
 */
class WebServiceAuthenticator extends AbstractFormLoginAuthenticator
{
    /**
     * @var Guzzle
     */
    private $client;

    /**
     * @var \Symfony\Bundle\FrameworkBundle\Routing\Router
     */
    private $router;

    /**
     * @var CsrfTokenManagerInterface
     */
    private $csrfTokenManager;

    /**
     * @var Request
     */
    private $request;

    /**
     * WebServiceAuthenticator constructor.
     *
     * @param Guzzle $client
     * @param ContainerInterface $container
     * @param CsrfTokenManagerInterface $csrfTokenManager
     * @param RequestStack $requestStack
     */
    public function __construct(
        Guzzle $client,
        ContainerInterface $container,
        CsrfTokenManagerInterface $csrfTokenManager,
        RequestStack $requestStack
    ) {
        $this->client = $client;
        $this->router = $container->get('router');
        $this->csrfTokenManager = $csrfTokenManager;
        $this->request = $requestStack->getCurrentRequest();
    }

    /**
     * @return string
     */
    public function getLoginUrl(): string
    {
        return $this->router->generate('login');
    }

    /**
     * Called on every request to decide if this authenticator should be
     * used for the request. Returning false will cause this authenticator
     * to be skipped.
     *
     * @param Request $request
     *
     * @return bool
     */
    public function supports(Request $request): bool
    {
        return $request->attributes->get('_route') === 'login' && $request->isMethod('POST');
    }

    /**
     * Called on every request. Return whatever credentials you want to
     * be passed to getUser() as $credentials.
     *
     * @param Request $request
     *
     * @return array
     */
    public function getCredentials(Request $request): array
    {
        $username = $request->get('_username');
        $password = $request->get('_password');

        $this->request = $request;

        return [
            'username' => $username,
            'password' => $password,
            'csrf_token' => $request->get('_csrf_token'),
        ];
    }

    /**
     * @param mixed $credentials
     * @param UserProviderInterface $userProvider
     *
     * @return WebServiceUser|null
     */
    public function getUser($credentials, UserProviderInterface $userProvider): ?WebServiceUser
    {
        $token = new CsrfToken('authenticate', $credentials['csrf_token']);
        if (!$this->csrfTokenManager->isTokenValid($token)) {
            throw new InvalidCsrfTokenException();
        }

        if (array_key_exists('username', $credentials) === false) {
            return null;
        }

        $username = $credentials['username'];
        $password = $credentials['password'];
        if (!$username) {
            return null;
        }

        $response = $this->authenticateByUserCredentials($username, $password);
        if ($response instanceof WebServiceUser) {
            return $response;
        }

        $response = json_decode($response->getContent(), true);

        throw new BadCredentialsException($response['error_description'] ?? 'Invalid credentials.');
    }

    /**
     * @param string $username
     * @param string $password
     *
     * @return WebServiceUser|Response
     */
    public function authenticateByUserCredentials(string $username, string $password)
    {
        $response = $this->client->auth([
            'grant_type' => 'password',
            'username' => $username,
            'password' => $password,
        ]);

        $responseData = $response->getJson();

        if ($response->getStatusCode() === Response::HTTP_CREATED) {
            return $this->loadUserData($username, $responseData);
        }

        return $response;
    }

    /**
     * @param string $username
     * @param string $refreshToken
     *
     * @return WebServiceUser|Response
     */
    public function authenticateByRefreshToken(string $username, string $refreshToken)
    {
        $response = $this->client->auth([
            'grant_type' => 'refresh_token',
            'refresh_token' => $refreshToken,
        ]);

        $responseData = $response->getJson();

        if ($response->getStatusCode() === Response::HTTP_CREATED) {
            return $this->loadUserData($username, $responseData);
        }

        return $response;
    }

    /**
     * @param string $username
     * @param array $token
     *
     * @return WebServiceUser|null
     */
    protected function loadUserData(string $username, array $token): ?WebServiceUser
    {
        // Creating new user instance
        $user = new WebServiceUser();
        $user->setUsername($username);

        // Storing last username in session
        $this->request->getSession()->set(Security::LAST_USERNAME, $username);

        // Storing access token in session
        if (array_key_exists('access_token', $token)) {
            $this->request->getSession()->set('Access-Token', $token['access_token']);
        }

        // Storing refresh token in session
        if (array_key_exists('refresh_token', $token)) {
            $this->request->getSession()->set('Refresh-Token', $token['refresh_token']);
        }

        // Getting user attributes from API
        $response = $this->client->get('/users/getByUsername', ['username' => $username]);
        if ($response->getStatusCode() === Response::HTTP_OK) {
            $data = $response->getJson();

            foreach ($data as $key => $value) {
                $method = 'set' . ucfirst($key);
                if (method_exists($user, $method)) {
                    $user->$method($value);
                }
            }

            return $user;
        }

        return null;
    }

    /**
     * @param mixed $credentials
     * @param UserInterface $user
     *
     * @return bool
     */
    public function checkCredentials($credentials, UserInterface $user): bool
    {
        // Credentials are checked via API. Return true.
        return true;
    }

    /**
     * @param Request $request
     * @param TokenInterface $token
     * @param string $providerKey
     *
     * @return Response
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey): Response
    {
        $route = $this->router->generate('site_index');
        $response = new RedirectResponse($route);

        if ($request->isXmlHttpRequest()) {
            $response = new JsonResponse([
                'success' => true,
                'redirect' => $route
            ]);
        }

        if ($refreshToken = $request->getSession()->get('Refresh-Token')) {
            $refreshToken = base64_encode($refreshToken . getenv('APP_SECRET'));
            $response->headers->setCookie(new Cookie('REFRESH_TOKEN', $refreshToken, (new \DateTime())->modify('+30 days')));
        }

        return $response;
    }

    /**
     * @param Request $request
     * @param AuthenticationException $exception
     *
     * @return Response
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response
    {
        if ($request->isXmlHttpRequest()) {
            return new JsonResponse([
                'success' => false,
                'message' => $exception->getMessage(),
                'code' => $exception->getCode()
            ]);
        }

        return new RedirectResponse($this->router->generate('login'));
    }
}