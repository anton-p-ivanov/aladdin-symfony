<?php

namespace App\Resource;

/**
 * Class Resource
 * @package App\Resource
 */
class Resource implements ResourceInterface
{
    /**
     * @var string
     */
    protected $resource;

    /**
     * @return string
     */
    public function getResource(): string
    {
        return $this->resource;
    }

    /**
     * @param array $attributes
     *
     * @return Resource
     */
    public static function create(array $attributes): self
    {
        $instance = new static;
        foreach ($attributes as $name => $value) {
            $method = 'set' . ucfirst($name);
            if (method_exists($instance, $method)) {
                $instance->$method($value);
            }
        }

        return $instance;
    }
}