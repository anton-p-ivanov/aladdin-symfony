<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Class JsonDecode
 * @package App\Twig
 */
class JsonDecode extends AbstractExtension
{
    /**
     * @return array|\Twig_Filter[]
     */
    public function getFilters()
    {
        return [
            new TwigFilter('json_decode', [$this, 'jsonDecode'])
        ];
    }

    /**
     * @param mixed $value
     * @param int $decimals
     *
     * @return string
     */
    public function jsonDecode($value)
    {
        return json_decode($value, true);
    }
}