<?php

namespace App\Service\Guzzle;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class Guzzle
 * @package App\Service\Guzzle
 */
class Guzzle
{
    /**
     * @var ContainerInterface
     */
    public $container;

    /**
     * @var Client
     */
    private $client;

    /**
     * @var \Symfony\Component\HttpFoundation\Request
     */
    private $request;

    /**
     * Guzzle constructor.
     *
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack, ContainerInterface $container)
    {
        // Storing current request in a variable
        $this->request = $requestStack->getCurrentRequest();

        // Preparing client
        $this->client = new Client($this->getConfig());

        // Container interface
        $this->container = $container;
    }

    /**
     * @return array
     */
    public function getConfig(): array
    {
        return [
            'base_uri' => "http://api-symfony.web.host",
            'auth' => [
                'a354f75b-0352-42d0-b1a5-168f2cb04a7f',
                'client_secret'
            ],
            'timeout' => 30,
            'headers' => [
                'Accept' => "application/json",
                'Accept-Language' => $this->request->getLocale(),
                'User-Agent' => "Guzzle/7.0",
            ]
        ];
    }

    /**
     * @param string $url URL
     * @param array $params
     * @return Response
     */
    public function delete($url, $params = []): Response
    {
        return $this->request('DELETE', $url, $params);
    }

    /**
     * @param string $url URL
     * @param array $params request body
     * @param array $headers
     *
     * @return Response
     */
    public function get($url, $params = [], $headers = []): Response
    {
        $headers['Search-Conditions'] = json_encode($params);

        return $this->request('GET', $url, ['headers' => $headers]);
    }

    /**
     * @param string $url URL
     * @param array $params
     * @return Response
     */
    public function post($url, $params = []): Response
    {
        return $this->request('POST', $url, ['json' => $params]);
    }

    /**
     * @param string $url URL
     * @param array $params
     * @return Response
     */
    public function put($url, $params = []): Response
    {
        return $this->request('PUT', $url, ['json' => $params]);
    }

    /**
     * @param array $params
     * @return Response
     */
    public function auth($params = []): Response
    {
        return $this->post('/oauth/token', $params);
    }

    /**
     * @param string $method
     * @param string $uri
     * @param array $options
     *
     * @return \Symfony\Component\HttpFoundation\Response|Response
     */
    public function request(string $method, string $uri, array $options = [])
    {
        $accessToken = $this->request->getSession()->get('Access-Token');
        if ($accessToken) {
            $options['headers']['Access-Token'] = $accessToken;
        }
        else {
            // for DEV only
            $options['headers']['Access-Token'] = '05faa7751d1b34f1e350a7bffa93e6f134233930';
        }

        try {
            /* @var \GuzzleHttp\Psr7\Response $response */
            $response = $this->client->request($method, $uri, $options);
        }
        catch (ClientException $exception) {
            $response = $exception->getResponse();
            $responseData = json_decode($response->getBody()->getContents(), true);

            switch ($response->getStatusCode()) {
                case 401:
                    if ($responseData['error'] === 'invalid_access_token') {
                        if ($this->refreshAccessToken()) {
                            return $this->request($method, $uri, $options);
                        }

                        return $this->logout();
                    }
                    break;
                default:
                    break;
            }
        }
        catch (ServerException $exception) {
            $response = $exception->getResponse();

            echo (string) $response->{'getBody'}();die();
        }

        return new Response(
            (string) $response->getBody(),
            $response->getStatusCode(),
            $response->getHeaders()
        );
    }

    /**
     * @return RedirectResponse
     */
    private function logout(): RedirectResponse
    {
        // logout user, clearing session and return to login page
        $this->container->get('security.token_storage')->setToken(null);
        $this->request->getSession()->invalidate();

        $route = $this->container->get('router')->generate('login');
        $response = new RedirectResponse($route);

        foreach ($response->headers->getCookies() as $cookie) {
            $response->headers->clearCookie($cookie->getName());
        }

        return $response->send();
    }

    /**
     * @return bool
     */
    private function refreshAccessToken(): bool
    {
        $refreshToken = $this->request->cookies->get('REFRESH_TOKEN');

        if ($refreshToken) {
            $refreshToken = substr(base64_decode($refreshToken), 0, -strlen(getenv('APP_SECRET')));

            $response = $this->auth([
                'grant_type' => 'refresh_token',
                'refresh_token' => $refreshToken,
            ]);

            $responseData = $response->getJson();

            if ($response->getStatusCode() === Response::HTTP_CREATED) {
                $this->request->getSession()->set('Access-Token', $responseData['access_token']);
                return true;
            }
        }

        return false;
    }
}