<?php

namespace App\Security\User;

use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class WebServiceUser
 * @package App\Entity
 */
class WebServiceUser implements UserInterface, EquatableInterface, \Serializable
{
    /**
     * @var string
     */
    private $fname;

    /**
     * @var string
     */
    private $lname;

    /**
     * @var string
     */
    private $fullName;

    /**
     * @var bool
     */
    private $isActive;

    /**
     * @var bool
     */
    private $isConfirmed;

    /**
     * @var bool
     */
    private $isExpired;

    /**
     * @var null|string
     */
    private $username;

    /**
     * @var null|string
     */
    private $password;

    /**
     * @var null|string
     */
    private $salt;

    /**
     * @var array
     */
    private $roles;

    /**
     * @var array
     */
    private $sites;

    /**
     * WebServiceUser constructor.
     */
    public function __construct()
    {
        $attributes = [
            'roles' => [],
            'sites' => [],
            'isExpired' => true,
            'isActive' => false,
            'isConfirmed' => false
        ];

        foreach ($attributes as $key => $value) {
            $this->{'set' . ucfirst($key)}($value);
        }
    }

    /**
     * @return string|null
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * @return null|string
     */
    public function getSalt(): ?string
    {
        return $this->salt;
    }

    /**
     * @return string
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }

    /**
     * @param null|string $username
     */
    public function setUsername(?string $username): void
    {
        $this->username = $username;
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

    /**
     * @see \Serializable::serialize()
     */
    public function serialize()
    {
        return serialize(
            [
                $this->fname,
                $this->lname,
                $this->fullName,
                $this->username,
                $this->roles,
            ]
        );
    }

    /**
     * @param $serialized
     *
     * @see \Serializable::unserialize()
     */
    public function unserialize($serialized)
    {
        list (
            $this->fname,
            $this->lname,
            $this->fullName,
            $this->username,
            $this->roles
            ) = unserialize($serialized);
    }

    /**
     * @param UserInterface $user
     *
     * @return bool
     */
    public function isEqualTo(UserInterface $user)
    {
        if (!$user instanceof WebServiceUser) {
            return false;
        }

        if ($this->username !== $user->getUsername()) {
            return false;
        }

        return true;
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {
        /* @todo Implementation needed */
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->isActive;
    }

    /**
     * @return bool
     */
    public function isConfirmed(): bool
    {
        return $this->isConfirmed;
    }

    /**
     * @param bool $isActive
     */
    public function setIsActive(bool $isActive): void
    {
        $this->isActive = $isActive;
    }

    /**
     * @param bool $isConfirmed
     */
    public function setIsConfirmed(bool $isConfirmed): void
    {
        $this->isConfirmed = $isConfirmed;
    }

    /**
     * @return array
     */
    public function getSites(): array
    {
        return $this->sites;
    }

    /**
     * @param array $sites
     */
    public function setSites(array $sites): void
    {
        $this->sites = $sites;
    }

    /**
     * @return bool
     */
    public function isExpired(): bool
    {
        return $this->isExpired;
    }

    /**
     * @param bool $isExpired
     */
    public function setIsExpired(bool $isExpired): void
    {
        $this->isExpired = $isExpired;
    }

    /**
     * @return string
     */
    public function getFname(): string
    {
        return $this->fname;
    }

    /**
     * @param string $fname
     */
    public function setFname(string $fname): void
    {
        $this->fname = $fname;
    }

    /**
     * @return string
     */
    public function getLname(): string
    {
        return $this->lname;
    }

    /**
     * @param string $lname
     */
    public function setLname(string $lname): void
    {
        $this->lname = $lname;
    }

    /**
     * @return string
     */
    public function getFullName(): string
    {
        return $this->fullName;
    }

    /**
     * @param string $fullName
     */
    public function setFullName(string $fullName): void
    {
        $this->fullName = $fullName;
    }
}
