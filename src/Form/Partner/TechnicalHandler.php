<?php

namespace App\Form\Partner;

/**
 * Class TechnicalHandler
 *
 * @package App\Form\Partner
 */
class TechnicalHandler extends PartnerHandler
{
    /**
     * @var string
     */
    protected $type = 'technical';

    /**
     * @param array $conditions
     *
     * @return array
     */
    protected function prepareConditions(array $conditions): array
    {
        $prepared = $this->getBaseConditions();
        $prepared['catalog'] = 'technical';
        $prepared['order'] = 'title';

        foreach ($conditions as $name => $value) {
            if (!$value || $name === 'page') {
                continue;
            }

            switch ($name) {
                case "name":
                    $prepared["~title"] = $value;
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
}