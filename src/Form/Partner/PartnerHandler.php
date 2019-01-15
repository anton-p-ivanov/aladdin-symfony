<?php

namespace App\Form\Partner;

use App\Repository\Repository;
use App\Tools\Paginator;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class PartnerHandler
 *
 * @package App\Form\Partner
 */
abstract class PartnerHandler
{
    /**
     * @var string
     */
    protected $type;

    /**
     * @var Repository
     */
    protected $repository;

    /**
     * @var \Symfony\Component\HttpFoundation\Request
     */
    protected $request;

    /**
     * PartnerHandler constructor.
     *
     * @param Repository $repository
     * @param Request $request
     */
    public function __construct(Repository $repository, Request $request)
    {
        // Storing guzzle client instance
        $this->repository = $repository;

        // Storing current request for future use
        $this->request = $request;
    }

    /**
     * @param array $conditions
     *
     * @return array
     */
    abstract protected function prepareConditions(array $conditions): array;

    /**
     * @param array $conditions
     *
     * @return Paginator
     */
    public function search(array $conditions = []): Paginator
    {
        $conditions = $this->prepareConditions($conditions);

        $className = '\\App\\Resource\\Partner\\' . ucfirst($this->type);
        $response = $this->repository->get($className)->find($conditions, true);

        return new Paginator($response);
    }

    /**
     * @return array
     */
    protected function getBaseConditions(): array
    {
        return [
            'type' => 'E',
            'order' => 'sort,title',
            'headers' => [
                'X-Pagination-Page' => (int) $this->request->get('page', 1),
                'X-Pagination-Size' => 10
            ]
        ];
    }
}