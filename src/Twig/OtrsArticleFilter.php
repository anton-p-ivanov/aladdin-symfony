<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Class OtrsArticleFilter
 * @package App\Twig
 */
class OtrsArticleFilter extends AbstractExtension
{
    /**
     * @return array|\Twig_Filter[]
     */
    public function getFilters()
    {
        return [
            new TwigFilter('otrsArticle', [$this, 'format'])
        ];
    }

    /**
     * @param string $value
     *
     * @return string
     */
    public function format($value): string
    {
        return nl2br(preg_replace(['/^>.*$/m', '/[\r\n]{2,}/', '/(\s){2,}/'], ['', "\n\n", "$1"], $value));
    }
}