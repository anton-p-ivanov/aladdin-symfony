<?php

namespace App\Controller\Company\Pressroom;

use App\Repository\Repository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ArticlesController
 *
 * @Route("/company/pressroom/articles")
 *
 * @package App\Controller\Company\Pressroom
 */
class ArticlesController extends BaseController
{
    /**
     * @var string
     */
    protected $catalog = 'articles';

    /**
     * @Route("/{page<\d+>?1}", name="pressroom:articles")
     *
     * @param Request $request
     * @param Repository $repository
     *
     * @return Response
     */
    public function index(Request $request, Repository $repository): Response
    {
        return parent::index($request, $repository);
    }

    /**
     * @Route("/tags/{tag<[\w\-]+>}/{page<\d+>?1}", name="pressroom:articles:tags")
     *
     * @param Request $request
     * @param Repository $repository
     *
     * @return Response
     */
    public function section(Request $request, Repository $repository): Response
    {
        return parent::section($request, $repository);
    }

    /**
     * @Route("/{code<[\w\-\_]+>}", name="pressroom:articles:view")
     *
     * @param Request $request
     * @param Repository $repository
     *
     * @return Response
     */
    public function view(Request $request, Repository $repository): Response
    {
        return parent::view($request, $repository);
    }
}