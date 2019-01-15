<?php

namespace App\Repository;

use App\Resource\Resource;
use App\Resource\User;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class UserRepository
 *
 * @package App\Repository
 */
class UserRepository extends RestRepository
{
    /**
     * @var string
     */
    protected $modelClass = User::class;

    /**
     * @param string $username
     *
     * @return User|Resource|null
     */
    public function findByUsername(string $username): ?User
    {
        $response = $this->client->get('/users/getByUsername', ['username' => $username]);

        if ($response && $response->getStatusCode() === Response::HTTP_OK) {
            return $this->createResource($response);
        }

        return null;
    }
}
