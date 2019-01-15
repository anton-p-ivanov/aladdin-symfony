<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\RequestStack;

class Formatter
{
    /**
     * @var string
     */
    private $locale;

    /**
     * SizeFilter constructor.
     *
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack)
    {
        if ($request = $requestStack->getCurrentRequest()) {
            $this->locale = $request->getLocale();
        }
    }

    /**
     * @param float $value
     * @param int $decimals
     *
     * @return string
     */
    public function asDecimal(float $value, $decimals = 2): string
    {
        $formatter = new \NumberFormatter($this->locale, \NumberFormatter::DECIMAL);
        $formatter->setAttribute(\NumberFormatter::MIN_FRACTION_DIGITS, 0);
        $formatter->setAttribute(\NumberFormatter::MAX_FRACTION_DIGITS, $decimals);

        return $formatter->format($value);
    }
}