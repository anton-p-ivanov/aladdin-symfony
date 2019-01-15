<?php

namespace App\Security\User;

use App\Service\Guzzle\Guzzle;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class WebServiceUserChecker
 * @package App\Security\User
 */
class WebServiceUserChecker implements UserCheckerInterface
{
    /**
     * @var Guzzle
     */
    private $client;

    /**
     * WebServiceUserChecker constructor.
     *
     * @param Guzzle $guzzle
     */
    public function __construct(Guzzle $guzzle)
    {
        $this->client = $guzzle;
    }

    /**
     * @param UserInterface $user
     */
    public function checkPreAuth(UserInterface $user)
    {
        $site_uuid = array_shift($this->client->getConfig()['auth']);

        if (!$user instanceof WebServiceUser) {
            return;
        }

        if (!$user->isActive()) {
            throw new CustomUserMessageAuthenticationException('User profile is not active.', [], 100);
        }

        if (!$user->isConfirmed()) {
            throw new CustomUserMessageAuthenticationException('User profile is not confirmed.', [], 101);
        }

        if ($user->isExpired()) {
            throw new CustomUserMessageAuthenticationException('User password has been expired.', [], 102);
        }

        if (!in_array($site_uuid, $user->getSites())) {
            throw new CustomUserMessageAuthenticationException('User does not have privileges to access this site.', [], 103);
        }
    }

    /**
     * @param UserInterface $user
     */
    public function checkPostAuth(UserInterface $user)
    {

    }
}