<?php

namespace App\Form\Support;

use App\Service\Otrs\Client;

/**
 * Class TicketHandler
 *
 * @package App\Form\Support
 */
class TicketHandler
{
    /**
     * @var array
     */
    public static $queues = [
        20 => [
            'QueueID' => 20,
            'EMail' => 'support.jc@aladdin-rd.ru',
            'Subject' => 'JaCarta TS Request {{DATE}}'
        ],
        21 => [
            'QueueID' => 21,
            'EMail' => 'support.jms@aladdin-rd.ru',
            'Subject' => 'JMS TS Request {{DATE}}'
        ],
        7 => [
            'QueueID' => 7,
            'EMail' => 'support.etoken@aladdin-rd.ru',
            'Subject' => 'eToken TS Request {{DATE}}'
        ],
        25 => [
            'QueueID' => 25,
            'EMail' => 'support.sam@aladdin-rd.ru',
            'Subject' => 'SAM TS Request {{DATE}}'
        ],
        5 => [
            'QueueID' => 5,
            'EMail' => 'support.sd@aladdin-rd.ru',
            'Subject' => 'SecretDisk TS Request {{DATE}}'
        ],
        27 => [
            'QueueID' => 27,
            'EMail' => 'support.db@aladdin-rd.ru',
            'Subject' => 'CryptoDB TS Request {{DATE}}'
        ],
        6 => [
            'QueueID' => 6,
            'EMail' => 'support.sd@aladdin-rd.ru',
            'Subject' => 'Aladdin TS Request {{DATE}}'
        ],
    ];

    /**
     * @var array
     */
    private $errors = [];

    /**
     * @var Client
     */
    private $client;

    /**
     * @var array
     */
    private $_productQueues = [
        'JaCarta PKI' => 20,
        'JaCarta PKI/BIO' => 20,
        'JaCarta PKI/ГОСТ' => 20,
        'JaCarta PKI/Flash' => 20,
        'JaCarta PKI/ГОСТ/Flash' => 20,
        'JaCarta ГОСТ' => 20,
        'JaCarta ГОСТ/Flash' => 20,
        'eToken PRO (Java)' => 7,
        'eToken ГОСТ' => 7,
        'eToken NG-FLASH (Java)' => 7,
        'eToken NG-OTP (Java)' => 7,
        'eToken PASS' => 7,
        'Secret Disk 5' => 5,
        'Secret Disk 4' => 5,
        'Secret Disk 4 Workgroup Edition' => 5,
        'Secret Disk Server' => 5,
        'Secret Disk Enterprise' => 5,
        'JaCarta Management System' => 21,
        'SafeNet Authentication Manager' => 25,
        '"Крипто БД"' => 27,
        'Прочие продукты' => 6
    ];

    /**
     * @var int
     */
    public static $defaultQueue = 6;

    /**
     * TicketHandler constructor.
     *
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param array $params
     *
     * @return mixed
     */
    public function create(array $params)
    {
//        $query = new Query(Ticket::className());
//        $attachment = null;
        $queueID = $this->getQueueID($params['product']);
//
//        if ($this->file) {
//            try {
//                $attachment = Json::decode($this->file);
//                if (is_array($attachment)) {
//                    $attachment = $attachment[0];
//                }
//            }
//            catch (\Exception $exception) {
//                $attachment = null;
//            }
//        }

        $this->client->addParam('Ticket', [
            'Title' => $params['subject'],
            'QueueID' => $this->client->isTesting() ? 22 : $queueID,
            'PriorityID' => 3,
            'StateID' => 1,
            'CustomerUser' => $params['email'],
            'CustomerID' => $params['email'],
        ]);

        $this->client->addParam('Article', [
            'From' => $params['email'],
            'Subject' => preg_replace('/\{\{DATE\}\}/', date('d.m.Y H:i'), self::$queues[$queueID]['Subject']),
            'Body' => $this->getMessage($params),
            'SenderType' => 'customer',
            'ContentType' => 'text/plain; charset=utf8',
            'To' => self::$queues[$queueID]['EMail'],
        ]);

//        if ($attachment) {
//            $query->params['Attachment'] = [
//                'Content' => base64_encode(file_get_contents(\Yii::$app->params['upload_dir'] . $attachment['name'])),
//                'Filename' => $attachment['name'],
//                'ContentType' => $attachment['type']
//            ];
//        }

        return $this->client->getClient()->__soapCall('TicketCreate', $this->client->prepareParams());
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @param $product
     *
     * @return int|mixed
     */
    protected function getQueueID($product)
    {
        if (isset($this->_productQueues[$product])) {
            return $this->_productQueues[$product];
        }

        return static::$defaultQueue;
    }

    /**
     * @param array $params
     *
     * @return string
     */
    protected function getMessage(array $params): string
    {
        return "Дата - " . (new \DateTime())->format('d.m.Y H:i:s') . "\n\n" .

            "Контактная информация:\n" .
            "----------------------\n" .
            "Имя                  : " . $params['fname'] . "\n" .
            "Фамилия              : " . $params['lname'] . "\n" .
            "Название организации : " . $params['company'] . "\n" .
            "Телефон              : " . $params['phone'] . "\n" .
            "Email                : " . $params['email'] . "\n\n" .

            "Информация о продукте:\n" .
            "----------------------\n" .
            "Продукт              :  " . $params['product'] . "\n" .
            "Используемое ПО      :  " . TicketType::$drivers[$params['driver']] . "\n" .
            "Версия ПО            :  " . $params['version'] . "\n\n" .

            "Партнёр / поставщик  :  " . $params['partner'] . "\n" .
            "Операционная система :  " . TicketType::$oses[$params['os']] . "\n\n" .

            "Сообщение:\n" .
            "----------------------\n" .
            strip_tags($params['content']);
    }
}