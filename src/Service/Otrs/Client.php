<?php

namespace App\Service\Otrs;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class Client
 * @package App\Service\Otrs
 */
class Client
{
    /**
     * @var array
     */
    private $params;

    /**
     * @var \SoapClient
     */
    protected $client;

    /**
     * Client constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $params = $container->getParameter('soap');
        $this->client = new \SoapClient(
            null, [
                'location' => $params['hostname'] . $params['path'],
                'uri' => $params['hostname'],
                'login' => $params['login'],
                'password' => $params['password'],
                'use' => SOAP_ENCODED,
                'style' => SOAP_RPC,
                'trace' => 1,
            ]
        );

        $this->setParams([
            'UserLogin' => $params['login'],
            'Password' => $params['password'],
            'Extended' => 1,
        ]);
    }

    /**
     * @param array $conditions
     *
     * @return Client
     */
    public function where(array $conditions = []): self
    {
        foreach ($conditions as $k => $v) {
            $this->addParam($k, $v);
        }

        return $this;
    }

    /**
     * @param array $params
     */
    protected function setParams(array $params): void
    {
        $this->params = $params;
    }

    /**
     * @param $name
     * @param $value
     */
    protected function addParam($name, $value)
    {
        $this->params[$name] = $value;
    }

    /**
     * @return array
     */
    protected function prepareParams(): array
    {
        $params = [];
        foreach ($this->params as $p => $v) {
            $params[] = new \SoapParam($v, $p);
        }

        return $params;
    }

    /**
     * @return mixed
     */
    protected function execute()
    {
        // Search for tickets IDs first
        $ticketIDs = $this->client->__soapCall('TicketSearch', $this->prepareParams());

        if (!$ticketIDs) {
            return null;
        }

        if (is_array($ticketIDs)) {
            $ticketIDs = implode(',', $ticketIDs['TicketID']);
        }

        $this->params['TicketID'] = $ticketIDs;
        $response = $this->client->__soapCall('TicketGet', $this->prepareParams());

        if (!is_array($response)) {
            $response = ['Ticket' => [$response]];
        }

        foreach ($response['Ticket'] as &$data) {
            $ticket = new Ticket();
            foreach ($data as $name => $value) {
                $method = 'set' .ucfirst($name);
                if (method_exists($ticket, $method)) {
                    $ticket->$method($value);
                }
            }

            $data = $ticket;
        }

        return $response['Ticket'];
    }

    /**
     * @param int $limit
     *
     * @return Client
     */
    public function limit(int $limit = 10): self
    {
        $this->addParam('Limit', $limit);

        return $this;
    }

    /**
     * @param array $data
     *
     * @return Client
     */
    public function order(array $data): self
    {
        $params = [
            'SortBy' => [],
            'OrderBy' => []
        ];

        foreach ($data as $sort => $order) {
            $params['SortBy'][] = $sort;
            $params['OrderBy'][] = ($order === SORT_ASC) ? 'Up' : 'Down';
        }

        $this->addParam('SortBy', $params['SortBy']);
        $this->addParam('OrderBy', $params['OrderBy']);

        return $this;
    }

    /**
     * @return Ticket[]
     */
    public function all(): array
    {
        return $this->execute();
    }

    /**
     * @param $ticket_id
     * @param array $additionalParams
     * @return Ticket
     */
    public function one($ticket_id, $additionalParams = []): Ticket
    {
        $this->addParam('TicketID', $ticket_id);

        if ($additionalParams) {
            $this->params = array_merge($this->params, $additionalParams);
        }

        $response = $this->client->__soapCall('TicketGet', $this->prepareParams());

        $ticket = new Ticket();
        foreach ($response as $name => $value) {
            $method = 'set' .ucfirst($name);
            if (method_exists($ticket, $method)) {
                $ticket->$method($value);
            }
        }

        return $ticket;
    }
}