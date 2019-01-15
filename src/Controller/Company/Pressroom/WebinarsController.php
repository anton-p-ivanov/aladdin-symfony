<?php

namespace App\Controller\Company\Pressroom;

use App\Repository\Repository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class WebinarsController
 *
 * @Route("/company/pressroom/webinars")
 *
 * @package App\Controller\Company\Pressroom
 */
class WebinarsController extends BaseController
{
    /**
     * @var string
     */
    protected $catalog = 'webinars';

    /**
     * @Route("/{page<\d+>?1}", name="pressroom:webinars")
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
     * @Route("/{code<[\w\-\_]+>}", name="pressroom:webinars:view")
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

    /**
     * @param Request $request
     *
     * @return array
     */
    protected function getBaseConditions(Request $request): array
    {
        return [
            'catalog' => $this->catalog,
            'type' => 'E',
            'order' => '-activeFrom',
            'headers' => [
                'X-Pagination-Page' => (int) $request->get('page', 1),
                'X-Pagination-Size' => 10
            ]
        ];
    }
}