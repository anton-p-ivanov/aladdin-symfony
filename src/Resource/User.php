<?php

namespace App\Resource;

/**
 * Class User
 * @package App\Resource
 */
class User extends Resource
{
    /**
     * @var string|null
     */
    private $fname;

    /**
     * @var string|null
     */
    private $lname;

    /**
     * @var string|null
     */
    private $email;

    /**
     * @return null|string
     */
    public function getFname(): ?string
    {
        return $this->fname;
    }

    /**
     * @return null|string
     */
    public function getLname(): ?string
    {
        return $this->lname;
    }

    /**
     * @return null|string
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param null|string $fname
     */
    public function setFname(?string $fname): void
    {
        $this->fname = $fname;
    }

    /**
     * @param null|string $lname
     */
    public function setLname(?string $lname): void
    {
        $this->lname = $lname;
    }

    /**
     * @param null|string $email
     */
    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }
}
