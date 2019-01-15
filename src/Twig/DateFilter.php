<?php

namespace App\Twig;

use App\Service\Formatter;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Class DateFilter
 * @package App\Twig
 */
class DateFilter extends AbstractExtension
{
    /**
     * @return array|\Twig_Filter[]
     */
    public function getFilters()
    {
        return [
            new TwigFilter('date_custom', [$this, 'formatDate'])
        ];
    }

    /**
     * @param string|\DateTime $value
     *
     * @return string
     */
    public function formatDate($value): string
    {
        if (is_string($value)) {
            $value = new \DateTime($value);
        }

        $months = [
            1 => 'января',
            2 => 'февраля',
            3 => 'марта',
            4 => 'апреля',
            5 => 'мая',
            6 => 'июня',
            7 => 'июля',
            8 => 'августа',
            9 => 'сентября',
            10 => 'октября',
            11 => 'ноября',
            12 => 'декабря',
        ];

        return sprintf("%s %s %s", $value->format('d'), $months[(int) $value->format('n')], $value->format('Y'));
    }
}