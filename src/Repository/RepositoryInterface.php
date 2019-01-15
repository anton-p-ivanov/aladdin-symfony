<?php

namespace App\Repository;

/**
 * Interface RepositoryInterface
 * @package App\Repository
 */
interface RepositoryInterface
{
    /**
     * @param string $primaryKey
     *
     * @return null|Repository
     */
    public function findOne(string $primaryKey): ?Repository;
}
