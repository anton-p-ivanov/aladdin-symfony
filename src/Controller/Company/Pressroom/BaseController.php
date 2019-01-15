<?php

namespace App\Controller\Company\Pressroom;

use App\Repository\Repository;
use App\Resource\Catalog\Element;
use App\Tools\Paginator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Adapter\TagAwareAdapter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class BaseController
 *
 * @package App\Controller\Company\Pressroom
 */
class BaseController extends AbstractController
{
    /**
     * @var string
     */
    protected $catalog;

    /**
     * @param string $view
     *
     * @return string
     */
    protected function getViewPath(string $view): string
    {
        return "company/pressroom/" . $this->catalog . "/$view";
    }

    /**
     * @return string
     */
    protected function getCacheKeyPrefix(): string
    {
        return "pressroom." . $this->catalog;
    }

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
        return $this->render($this->getViewPath('index.html.twig'), [
            'paginator' => $this->search($request, $repository),
        ]);
    }

    /**
     * @param Request $request
     * @param Repository $repository
     *
     * @return Response
     */
    public function section(Request $request, Repository $repository): Response
    {
        $conditions = [];

        $tags = explode('-', $request->get('tag'));
        $sections = $repository->get(Element::class)->find([
            'catalog' => $this->catalog,
            'code' => $tags,
            'type' => 'S',
            'limit' => 1,
        ]);

        if (!$sections) {
            throw new NotFoundHttpException('Invalid tags provided.');
        }

        $conditions['section'] = $this->getSectionCodes($sections);

        if ($year = $this->getYear($tags)) {
            $conditions = ['period' => [
                "from" => "$year-01-01 00:00:00",
                "to" => "$year-12-31 23:59:59",
            ]];
        }

        return $this->render($this->getViewPath('index.html.twig'), [
            'sections' => $sections,
            'paginator' => $this->search($request, $repository, $conditions),
        ]);
    }

    /**
     * @param array $tags
     *
     * @return null|string
     */
    protected function getYear(array $tags): ?string
    {
        $year = null;
        foreach ($tags as $index => $tag) {
            if (preg_match('/^\d{4}$/', $tag)) {
                $year = $tag;
                unset($tags[$index]);
            }
        }

        return $year;
    }

    /**
     * @param array $sections
     *
     * @return array
     */
    protected function getSectionCodes(array $sections): array
    {
        return array_map(function (Element $section) { return $section->getCode(); }, $sections);
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
            'order' => 'sort,-activeFrom',
            'headers' => [
                'X-Pagination-Page' => (int) $request->get('page', 1),
                'X-Pagination-Size' => 10
            ]
        ];
    }

    /**
     * @param Request $request
     * @param Repository $repository
     * @param array $conditions
     *
     * @return Paginator
     */
    protected function search(Request $request, Repository $repository, array $conditions = []): Paginator
    {
        $cache = new TagAwareAdapter(new FilesystemAdapter());
        $cacheTag = $this->getCacheKeyPrefix() . md5(json_encode($conditions));
        $cacheKey = $cacheTag . $request->get('page', 1);

        $cacheItem = $cache->getItem($cacheKey);
        $cacheItem->tag($cacheTag);
        $cacheItem->expiresAfter(\DateInterval::createFromDateString('1 hour'));

        $elements = $cacheItem->get();

        if (!$cacheItem->isHit() || $request->get('refresh')) {
            $conditions = array_merge($this->getBaseConditions($request), $conditions);
            $response = $repository->get(Element::class)->find($conditions, true);

            if ($elements != $response) {
                $cache->invalidateTags([$cacheTag]);
            }

            $elements = $response;
            $cache->save($cacheItem->set($elements));
        }
        else {
            $elements = $cacheItem->get();
        }

        return new Paginator($elements);
    }

    /**
     * @param Request $request
     * @param Repository $repository
     *
     * @return Response
     */
    public function view(Request $request, Repository $repository): Response
    {
        $cache = new FilesystemAdapter();
        $cacheKey = $this->getCacheKeyPrefix() . md5($request->get('code'));

        $cacheItem = $cache->getItem($cacheKey);
        $cacheItem->expiresAfter(\DateInterval::createFromDateString('1 month'));

        if (!$cacheItem->isHit() || $request->get('refresh')) {
            $elements = $repository->get(Element::class)->find([
                'catalog' => $this->catalog,
                'code' => $request->get('code'),
                'limit' => 1,
            ]);

            $element = array_shift($elements);
            $cache->save($cacheItem->set($element));
        }
        else {
            $element = $cacheItem->get();
        }

        return $this->render($this->getViewPath('view.html.twig'), [
            'element' => $element
        ]);
    }
}