<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

/**
 * Class TemplateController
 * @package App\Controller
 */
class TemplateController extends \Symfony\Bundle\FrameworkBundle\Controller\TemplateController
{
    private $request;

    /**
     * TemplateController constructor.
     *
     * @param RequestStack $requestStack
     * @param Environment|null $twig
     * @param EngineInterface|null $templating
     */
    public function __construct(RequestStack $requestStack, Environment $twig = null, EngineInterface $templating = null)
    {
        parent::__construct($twig, $templating);

        $this->request = $requestStack->getCurrentRequest();
    }

    /**
     * @param string $template
     * @param int|null $maxAge
     * @param int|null $sharedAge
     * @param bool|null $private
     *
     * @return Response
     */
    public function templateAction(string $template, int $maxAge = null, int $sharedAge = null, bool $private = null): Response
    {
        $template = preg_replace_callback('/\{([\w\-]+)\}/', function($matches) {
            return $this->request->get($matches[1]);
        }, $template);

        return parent::templateAction($template, $maxAge, $sharedAge, $private);
    }
}
