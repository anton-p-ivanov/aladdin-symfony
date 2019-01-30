<?php

namespace App\Service\Otrs;

/**
 * Class Ticket
 * @package App\Service\Otrs
 */
class Ticket
{
    /**
     * @var array
     */
    public static $states = [
        1 => 'Новое',
        2 => 'Решено',
        3 => 'Закрыто',
        4 => 'Открыто',
        5 => 'Удалено',
        6 => 'Ожидание',
        7 => 'Ожидание',
        8 => 'Ожидание',
        9 => 'Объединено',
    ];

    /**
     * @var array
     */
    public static $openedStates = [1, 4, 6, 7, 8, 9];

    /**
     * @var array
     */
    public static $closedStates = [2, 3, 5];

    /**
     * @var array
     */
    public static $statesColors = [
        1 => 'warning',
        2 => 'success',
        3 => 'danger',
        4 => 'default',
        5 => 'info',
        6 => 'info',
        7 => 'info',
        8 => 'info',
        9 => 'info',
    ];

    /**
     * @var string
     */
    private $StateID;

    /**
     * @var string
     */
    private $OwnerID;

    /**
     * @var string
     */
    private $Owner;

    /**
     * @var string
     */
    private $Title;

    /**
     * @var string
     */
    private $TicketNumber;

    /**
     * @var string
     */
    private $TicketID;

    /**
     * @var string
     */
    private $QueueID;

    /**
     * @var array
     */
    private $Article;

    /**
     * @var \DateTime
     */
    private $Created;

    /**
     * @var \DateTime|null
     */
    private $Closed;

    /**
     * @var \DateTime|null
     */
    private $Changed;

    /**
     * @return bool|mixed
     */
    public function isClosed(): bool
    {
        return in_array($this->StateID, self::$closedStates);
    }

    /**
     * @return string
     */
    public function getStateID(): string
    {
        return $this->StateID;
    }

    /**
     * @param string $StateID
     */
    public function setStateID(string $StateID): void
    {
        $this->StateID = $StateID;
    }

    /**
     * @return string
     */
    public function getQueueID(): string
    {
        return $this->QueueID;
    }

    /**
     * @param string $QueueID
     */
    public function setQueueID(string $QueueID): void
    {
        $this->QueueID = $QueueID;
    }

    /**
     * @return string
     */
    public function getOwnerID(): string
    {
        return $this->OwnerID;
    }

    /**
     * @param string $OwnerID
     */
    public function setOwnerID(string $OwnerID): void
    {
        $this->OwnerID = $OwnerID;
    }

    /**
     * @return string
     */
    public function getOwner(): string
    {
        return $this->Owner;
    }

    /**
     * @param string $Owner
     */
    public function setOwner(string $Owner): void
    {
        $this->Owner = $Owner;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->Title;
    }

    /**
     * @param string $Title
     */
    public function setTitle(string $Title): void
    {
        $this->Title = $Title;
    }

    /**
     * @return string
     */
    public function getTicketNumber(): string
    {
        return $this->TicketNumber;
    }

    /**
     * @param string $TicketNumber
     */
    public function setTicketNumber(string $TicketNumber): void
    {
        $this->TicketNumber = $TicketNumber;
    }

    /**
     * @return string
     */
    public function getTicketID(): string
    {
        return $this->TicketID;
    }

    /**
     * @param string $TicketID
     */
    public function setTicketID(string $TicketID): void
    {
        $this->TicketID = $TicketID;
    }

    /**
     * @return array|null
     */
    public function getArticle()
    {
        return $this->Article;
    }

    /**
     * @param array $Article
     */
    public function setArticle($Article): void
    {
        $this->Article = $Article;
    }

    /**
     * @return \DateTime
     */
    public function getCreated(): \DateTime
    {
        return $this->Created;
    }

    /**
     * @param string $Created
     */
    public function setCreated(string $Created): void
    {
        $this->Created = new \DateTime($Created);
    }

    /**
     * @return \DateTime
     */
    public function getClosed(): \DateTime
    {
        return $this->Closed;
    }

    /**
     * @param string $Closed
     */
    public function setClosed(string $Closed): void
    {
        $this->Closed = new \DateTime($Closed);
    }

    /**
     * @return \DateTime|null
     */
    public function getChanged(): ?\DateTime
    {
        return $this->Changed;
    }

    /**
     * @param string|null $Changed
     */
    public function setChanged(?string $Changed): void
    {
        if ($Changed) {
            $this->Changed = new \DateTime($Changed);
        }
    }

    /**
     * @return string
     */
    public function getState(): string
    {
        return self::$states[$this->getStateID()];
    }
}