<?php

namespace App\Resource\Partner;

use App\Resource\Catalog\Element;

/**
 * Class Element
 * @package App\Resource\Partner
 */
class Business extends Element
{
    /**
     * @var array
     */
    public $statuses = [
        4972 => 'Серебряный партнёр',
        4974 => 'Золотой партнёр',
        4970 => 'Платиновый партнёр',
        4976 => 'Реселлер',
        31272 => 'Премиум реселлер',
        5060 => 'Дилер',
        4968 => 'Дистрибьютор',
        31282 => 'Сервисный партнёр',
    ];

    /**
     * @param array $values
     */
    public function setValues(array $values): void
    {
        $matrix = [
            'ZIP' => null,
            'COUNTRY' => null,
            'STATE' => null,
            'CITY' => null,
            'STREET' => null,
            'URL' => null,
            'PHONE' => null,
            'SECTORS' => [],
            'STATUSES' => [],
            'RECOMMENDED' => false
        ];

        $values = array_merge($matrix, $values);

        if ($statuses = $values["STATUSES"]) {
            $statuses = is_string($statuses) ? [$statuses => ''] : array_flip($statuses);
            $values["STATUSES"] = array_intersect_key($this->statuses, $statuses);
        }

        $this->values = $values;
    }
}