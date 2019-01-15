<?php

namespace App\Resource\Storage;

use App\Resource\Resource;
use App\Resource\Workflow;

/**
 * Class Storage
 * @package App\Resource\Storage
 */
class Storage extends Resource
{
    /**
     * @var bool
     */
    public $isGranted = false;

    /**
     * @var string
     */
    protected $resource = "/storage";

    /**
     * @var string
     */
    private $uuid;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $description;

    /**
     * @var Workflow
     */
    private $workflow;

    /**
     * @var File
     */
    private $file;

    /**
     * @var array
     */
    private $roles;

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
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
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

    /**
     * @return File
     */
    public function getFile(): File
    {
        return $this->file;
    }

    /**
     * @param File|array $file
     */
    public function setFile($file): void
    {
        if (is_array($file)) {
            $file = File::create($file);
        }

        $this->file = $file;
    }

    /**
     * @return array
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * @param array $roles
     */
    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }
}