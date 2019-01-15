<?php

namespace App\Service\Guzzle;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response as BaseResponse;

/**
 * Class Response
 * @package App\Service\Guzzle
 */
class Response extends BaseResponse
{
    /**
     * @return bool
     */
    public function hasValidationErrors(): bool
    {
        return $this->getStatusCode() === self::HTTP_UNPROCESSABLE_ENTITY;
    }

    /**
     * @return array
     */
    public function getValidationErrors(): array
    {
        $response = json_decode($this->getContent(), true);

        if (array_key_exists('errors', $response)) {
            return $response['errors'];
        }

        return [];
    }

    /**
     * @param bool $asArray
     *
     * @return mixed
     * @throws \Exception
     */
    public function getJson(bool $asArray = true)
    {
        if ($this->headers->get('Content-Type') !== 'application/json') {
            throw new \Exception('Response content type is not `application/json`');
        }

        return json_decode($this->getContent(), $asArray);
    }

    /**
     * @param bool $json
     *
     * @return JsonResponse
     */
    public function asJson($json = false): JsonResponse
    {
        return new JsonResponse($this->getContent(), $this->getStatusCode(), $this->headers->all(), $json);
    }
}