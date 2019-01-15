<?php

namespace App\Twig;

use App\Service\Formatter;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Class SizeFilter
 * @package App\Twig
 */
class SizeFilter extends AbstractExtension
{
    /**
     * @var Formatter
     */
    private $formatter;

    /**
     * SizeFilter constructor.
     *
     * @param Formatter $formatter
     */
    public function __construct(Formatter $formatter)
    {
        $this->formatter = $formatter;
    }

    /**
     * @return array|\Twig_Filter[]
     */
    public function getFilters()
    {
        return [
            new TwigFilter('size', [$this, 'formatSize'])
        ];
    }

    /**
     * @param int $value
     * @param int $decimals
     * @param int $base
     *
     * @return string
     */
    public function formatSize(int $value, $decimals = 2, $base = 1024): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
        $iteration = 0;

        do {
            $value /= $base;
            if ($value > 1) {
                $iteration++;
            }

        } while ($value > $base);

        return sprintf("%d %s", $this->formatter->asDecimal($value, $decimals), $units[$iteration]);
    }
}