<?php

namespace App\Form;

use App\Service\Guzzle\Guzzle;
use App\Service\Guzzle\Response;

/**
 * Class BaseHandler
 *
 * @package App\Form
 */
class BaseHandler
{
    /**
     * @var string
     */
    protected $form_uuid;

    /**
     * @var Guzzle
     */
    private $client;

    /**
     * @var array
     */
    private $errors = [];

    /**
     * VacancyHandler constructor.
     *
     * @param Guzzle $guzzle
     */
    public function __construct(Guzzle $guzzle)
    {
        $this->client = $guzzle;
    }

    /**
     * @param array $params
     *
     * @return bool
     */
    public function send(array $params): bool
    {
        $response = $this->client->post('/forms/' . $this->form_uuid . '/results/', $params);

        if ($response instanceof Response) {
            if ($response->getStatusCode() == Response::HTTP_UNPROCESSABLE_ENTITY) {
                $this->errors = $response->getJson();

                return false;
            }

            return $response->getStatusCode() == Response::HTTP_CREATED;
        }

        return false;
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @param string $uuid
     */
    public function setFormUuid(string $uuid): void
    {
        $this->form_uuid = $uuid;
    }
}