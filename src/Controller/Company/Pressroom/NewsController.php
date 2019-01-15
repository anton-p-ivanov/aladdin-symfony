<?php

namespace App\Controller\Company\Pressroom;

use App\Repository\Repository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class NewsController
 *
 * @Route("/company/pressroom/news")
 *
 * @package App\Controller\Company\Pressroom
 */
class NewsController extends BaseController
{
    /**
     * @var string
     */
    protected $catalog = 'news';

    /**
     * @Route("/{page<\d+>?1}", name="pressroom:news")
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
     * @Route("/tags/{tag<[\w\-]+>}/{page<\d+>?1}", name="pressroom:news:tags")
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
     * @Route("/{code<[\w\-\_]+>}", name="pressroom:news:view")
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