<?php

namespace App\Repository;

use App\Resource\Resource;
use App\Security\Guzzle;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use GuzzleHttp\Psr7\Response;

/**
 * Class RestRepository
 *
 * @package App\Repository
 */
class RestRepository
{
    /**
     * @var Guzzle
     */
    protected $client;

    /**
     * @var string
     */
    protected $modelClass;

    /**
     * WebServiceUserProvider constructor.
     *
     * @param Guzzle $client
     */
    public function __construct(Guzzle $client)
    {
        $this->client = $client;
    }

    /**
     * @param Response $response
     *
     * @return null|Resource
     */
    protected function createResource(Response $response)
    {
        $attributes = json_decode($response->getBody(), true);

        $entity = new $this->modelClass;
        foreach ($attributes as $name => $value) {
            $method = 'set' . ucfirst($name);
            if (!method_exists($entity, $method)) {
                continue;
            }

            $entity->$method($value);
        }

        return $entity;
    }

    /**
     * @param Response $response
     *
     * @return Collection
     */
    protected function createCollection(Response $response): Collection
    {
        return new ArrayCollection();
    }
}
