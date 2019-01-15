<?php

namespace App\Repository;

use App\Resource\ResourceInterface;
use App\Service\Guzzle\Guzzle;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class Repository
 * @package App\Repository
 */
class Repository
{
    /**
     * @var Guzzle
     */
    protected $client;

    /**
     * @var ResourceInterface
     */
    protected $resource;

    /**
     * Repository constructor.
     *
     * @param Guzzle $guzzle
     */
    public function __construct(Guzzle $guzzle)
    {
        $this->client = $guzzle;
    }

    /**
     * @param string $repositoryName
     *
     * @return Repository
     */
    public function get($repositoryName): self
    {
        $this->resource = new $repositoryName;

        return $this;
    }

    /**
     * @param string $uuid
     *
     * @return ResourceInterface|null
     */
    public function findOne(string $uuid): ?ResourceInterface
    {
        $response = $this->client->get($this->resource->getResource() . "/$uuid");

        if ($response->getStatusCode() === Response::HTTP_NOT_FOUND) {
            return null;
        }

        $attributes = $response->getJson(true);
        foreach ($attributes as $name => $value) {
            $method = 'set' . ucfirst($name);

            if (method_exists($this->resource, $method)) {
                $this->resource->$method($value);
            }
        }

        return $this->resource;
    }

    /**
     * @param array $conditions
     * @param bool $withResponseHeaders
     *
     * @return array|null
     */
    public function find(array $conditions = [], $withResponseHeaders = false): ?array
    {
        $headers = [];
        if (array_key_exists('headers', $conditions)) {
            $headers = $conditions['headers'];
            unset($conditions['headers']);
        }

        $response = $this->client->get($this->resource->getResource(), $conditions, $headers);

        if ($response->getStatusCode() === Response::HTTP_OK) {
            $data = $response->getJson(true);

            foreach ($data as $index => $attributes) {
                $resource = clone $this->resource;
                foreach ($attributes as $name => $value) {
                    $method = 'set' . ucfirst($name);

                    if (method_exists($resource, $method)) {
                        $resource->$method($value);
                    }
                }

                $data[$index] = $resource;
            }

            if ($withResponseHeaders) {
                return [
                    'data' => $data,
                    'headers' => $response->headers
                ];
            }

            return $data;
        }

        return null;
    }
}
