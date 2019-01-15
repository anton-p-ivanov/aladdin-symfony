<?php

namespace App\Resource\Storage;

use App\Resource\Resource;
use App\Resource\Workflow;

/**
 * Class File
 * @package App\Resource\Storage
 */
class File extends Resource
{
    /**
     * @var string
     */
    protected $resource = "/storage/file";

    /**
     * @var string
     */
    private $uuid;

    /**
     * @var string
     */
    private $name;

    /**
     * @var int
     */
    private $size;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $hash;

    /**
     * @var Workflow
     */
    private $workflow;

    /**
     * @return string
     */
    public function getUuid(): string
    {
        return $this->uuid;
    }

    /**
     * @param string $uuid
     */
    public function setUuid(string $uuid): void
    {
        $this->uuid = $uuid;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return int
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * @param int $size
     */
    public function setSize(int $size): void
    {
        $this->size = $size;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getHash(): string
    {
        return $this->hash;
    }

    /**
     * @param string $hash
     */
    public function setHash(string $hash): void
    {
        $this->hash = $hash;
    }

    /**
     * @return Workflow
     */
    public function getWorkflow(): Workflow
    {
        return $this->workflow;
    }

    /**
     * @param Workflow|array $workflow
     */
    public function setWorkflow($workflow): void
    {
        if (is_array($workflow)) {
            $workflow = Workflow::create($workflow);
        }

        $this->workflow = $workflow;
    }
}