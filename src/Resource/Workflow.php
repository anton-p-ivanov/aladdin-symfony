<?php

namespace App\Resource;

/**
 * Class Workflow
 * @package App\Resource
 */
class Workflow extends Resource
{
    /**
     * @var \DateTime
     */
    private $createdAt;

    /**
     * @var \DateTime
     */
    private $updatedAt;

    /**
     * @var bool
     */
    private $isPublished;

    /**
     * @var bool
     */
    private $isDeleted;

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime|array $createdAt
     */
    public function setCreatedAt($createdAt): void
    {
        if (is_array($createdAt)) {
            $createdAt = new \DateTime($createdAt['date'], new \DateTimeZone($createdAt['timezone']));
        }

        $this->createdAt = $createdAt;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime|array $updatedAt
     */
    public function setUpdatedAt($updatedAt): void
    {
        if (is_array($updatedAt)) {
            $updatedAt = new \DateTime($updatedAt['date'], new \DateTimeZone($updatedAt['timezone']));
        }

        $this->updatedAt = $updatedAt;
    }

    /**
     * @return bool
     */
    public function isPublished(): bool
    {
        return $this->isPublished;
    }

    /**
     * @param bool $isPublished
     */
    public function setIsPublished(bool $isPublished): void
    {
        $this->isPublished = $isPublished;
    }

    /**
     * @return bool
     */
    public function isDeleted(): bool
    {
        return $this->isDeleted;
    }

    /**
     * @param bool $isDeleted
     */
    public function setIsDeleted(bool $isDeleted): void
    {
        $this->isDeleted = $isDeleted;
    }
}