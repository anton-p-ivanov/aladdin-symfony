<?php

namespace App\Resource\Catalog;

use App\Resource\Resource;

/**
 * Class Element
 * @package App\Resource\Catalog
 */
class Element extends Resource
{
    /**
     * @var string
     */
    protected $resource = "/catalogs/elements";

    /**
     * @var string
     */
    protected $uuid;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var string
     */
    protected $content;

    /**
     * @var string
     */
    protected $code;

    /**
     * @var int
     */
    protected $sort;

    /**
     * @var \DateTime
     */
    protected $activeFrom;

    /**
     * @var \DateTime
     */
    protected $activeTo;

    /**
     * @var array
     */
    protected $values;

    /**
     * @var array
     */
    protected $sections;

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
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @param string $content
     */
    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @param string $code
     */
    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    /**
     * @return int
     */
    public function getSort(): int
    {
        return $this->sort;
    }

    /**
     * @param int $sort
     */
    public function setSort(int $sort): void
    {
        $this->sort = $sort;
    }

    /**
     * @return \DateTime
     */
    public function getActiveFrom(): \DateTime
    {
        return $this->activeFrom;
    }

    /**
     * @param \DateTime|array $activeFrom
     */
    public function setActiveFrom($activeFrom): void
    {
        if (is_array($activeFrom)) {
            $activeFrom = new \DateTime($activeFrom['date'], new \DateTimeZone($activeFrom['timezone']));
        }

        $this->activeFrom = $activeFrom;
    }

    /**
     * @return \DateTime
     */
    public function getActiveTo(): \DateTime
    {
        return $this->activeTo;
    }

    /**
     * @param \DateTime|array $activeTo
     */
    public function setActiveTo($activeTo): void
    {
        if (is_array($activeTo)) {
            $activeTo = new \DateTime($activeTo['date'], new \DateTimeZone($activeTo['timezone']));
        }

        $this->activeTo = $activeTo;
    }

    /**
     * @return array
     */
    public function getValues(): array
    {
        return $this->values;
    }

    /**
     * @param array $values
     */
    public function setValues(array $values): void
    {
        $this->values = $values;
    }

    /**
     * @return array
     */
    public function getSections(): array
    {
        return $this->sections;
    }

    /**
     * @param array $sections
     */
    public function setSections(array $sections): void
    {
        $this->sections = $sections;
    }
}