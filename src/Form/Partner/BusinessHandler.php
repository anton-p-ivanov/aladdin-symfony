<?php

namespace App\Form\Partner;

/**
 * Class BusinessHandler
 *
 * @package App\Form\Partner
 */
class BusinessHandler extends PartnerHandler
{
    /**
     * @var string
     */
    protected $type = 'business';

    /**
     * @param array $conditions
     *
     * @return array
     */
    protected function prepareConditions(array $conditions): array
    {
        $prepared = $this->getBaseConditions();
        $prepared['catalog'] = 'partners';

        foreach ($conditions as $name => $value) {
            if (!$value || $name === 'page') {
                continue;
            }

            switch ($name) {
                case "name":
                    $prepared["~title"] = $value;
                    break;
                case "product":
                    if ($sectors = $this->prepareSectors($value)) {
                        $prepared["property_SECTORS"] = $sectors;
                    }
                    break;
                case "method":
                    $prepared["property_MEANS"] = $value;
                    break;
                default:
                    $prepared["property_" . strtoupper($name)] = $value;
                    break;
            }
        }

        if (!array_key_exists('property_COUNTRY', $prepared)) {
            $prepared['property_COUNTRY'] = 1;
            $prepared['property_CITY'] = 'Москва';
        }

        return $prepared;
    }

    /**
     * @param string $sector
     *
     * @return array|null
     */
    private function prepareSectors(string $sector): ?array
    {
        $sectors = null;

        switch ($sector) {
            case "4993":
                $sectors = ["4993", "44843"];
                break;
            case "4997":
                $sectors = ["4997", "44845", "44846"];
                break;
            case "4987":
                $sectors = ["4987", "24462"];
                break;
            case "4989":
                $sectors = ["4989", "4985"];
                break;
            case "39536":
                break;
            default:
                $sectors = $sector ? [$sector] : null;
                break;
        }

        return $sectors;
    }
}