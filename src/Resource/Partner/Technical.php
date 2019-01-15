<?php

namespace App\Resource\Partner;

use App\Resource\Catalog\Element;

/**
 * Class Technical
 * @package App\Resource\Partner
 */
class Technical extends Element
{
    /**
     * @var array
     */
    public $statuses = [
        8170 => 'Технологический партнёр',
        8172 => 'Сертифицированный технологический партнёр',
    ];

    /**
     * @param array $values
     */
    public function setValues(array $values): void
    {
        $matrix = [
            'ZIP' => null,
            'COUNTRY' => null,
            'REGION' => null,
            'CITY' => null,
            'STREET' => null,
            'URL' => null,
            'PHONE' => null,
            'STATUSES' => []
        ];

        $values = array_merge($matrix, $values);

        if ($statuses = $values["STATUSES"]) {
            $statuses = is_string($statuses) ? [$statuses => ''] : array_flip($statuses);
            $values["STATUSES"] = array_intersect_key($this->statuses, $statuses);
        }

        $this->values = $values;
    }
}