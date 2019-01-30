<?php

namespace App\Form\Support;

use App\Service\Otrs\Client;
use App\Service\Otrs\Ticket;

/**
 * Class MessageHandler
 *
 * @package App\Form\Support
 */
class MessageHandler
{
    /**
     * @var array
     */
    private $errors = [];

    /**
     * @var Client
     */
    private $client;

    /**
     * MessageHandler constructor.
     *
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param Ticket $ticket
     * @param array $params
     *
     * @return mixed
     */
    public function send($ticket, array $params)
    {
        $this->client->addParam('TicketID', $ticket->getTicketID());
        $this->client->addParam('Article', [
            'From' => $params['email'],
            'Subject' => MessageType::$subjects[$params['subject']],
            'Body' => $params['text'],
            'SenderType' => 'customer',
            'ContentType' => 'text/plain; charset=utf8',
            'To' => $this->getRecipient($ticket->getQueueID()),
        ]);

        return $this->client->getClient()->__soapCall('TicketUpdate', $this->client->prepareParams());
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @param $QueueID
     *
     * @return string
     */
    protected function getRecipient($QueueID): string
    {
        foreach (TicketHandler::$queues as $queue) {
            if ($queue['QueueID'] == $QueueID) {
                return $queue['EMail'];
            }
        }

        return 'support@aladdin-rd.ru';
    }
}