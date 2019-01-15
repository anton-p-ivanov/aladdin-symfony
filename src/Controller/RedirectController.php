<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class RedirectController
 * @package App\Controller
 */
class RedirectController extends \Symfony\Bundle\FrameworkBundle\Controller\RedirectController
{
    /**
     * @param Request $request
     * @param string $path
     * @param bool $permanent
     * @param string|null $scheme
     * @param int|null $httpPort
     * @param int|null $httpsPort
     * @param bool $keepRequestMethod
     *
     * @return Response
     */
    public function urlRedirectAction(Request $request, string $path, bool $permanent = false, string $scheme = null, int $httpPort = null, int $httpsPort = null, bool $keepRequestMethod = false): Response
    {
        $path = preg_replace_callback('/\{([\w\-]+)\}/', function($matches) use ($request) {
            return $request->get($matches[1]);
        }, $path);

        return parent::urlRedirectAction($request, $path, $permanent, $scheme, $httpPort, $httpsPort, $keepRequestMethod);
    }
}
