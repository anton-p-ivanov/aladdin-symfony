<?php

namespace App\Controller\Company\Pressroom;

use App\Repository\Repository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class EventsController
 *
 * @Route("/company/pressroom/events")
 *
 * @package App\Controller\Company\Pressroom
 */
class EventsController extends BaseController
{
    /**
     * @var string
     */
    protected $catalog = 'events';

    /**
     * @Route("/{page<\d+>?1}", name="pressroom:events")
     *
     * @param Request $request
     * @param Repository $repository
     *
     * @return Response
     */
    public function index(Request $request, Repository $repository): Response
    {
        $future = null;

        if ((int) $request->get('page', 1) === 1) {
            $future = $this->search($request, $repository, ['period' => [
                "from" => (new \DateTime())->format("Y-m-d H:i:s"),
                "order" => "sort,activeFrom",
            ]]);
        }

        $past = $this->search($request, $repository, ['period' => [
            "to" => (new \DateTime())->format("Y-m-d H:i:s"),
        ]]);

        return $this->render('company/pressroom/events/index.html.twig', [
            'future' => $future,
            'paginator' => $past
        ]);
    }

    /**
     * @Route("/tags/{tag<[\w\-]+>}/{page<\d+>?1}", name="pressroom:events:tags")
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
     * @Route("/{code<[\w\-\_]+>}", name="pressroom:events:view")
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