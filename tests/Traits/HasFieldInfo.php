<?php

namespace AsaasPaymentGateway\Tests\Traits;

use AsaasPaymentGateway\Helpers\Utils;

trait HasFieldInfo
{
    public function getFieldInfos(): array
    {
        $fieldMappings = [];
        foreach ($this->basicAtributes as $attribute) {
            $attrData = Utils::generateGetterSetterArray($attribute);
            $index = $attrData['attribute'];
            unset($attrData['attribute']);
            $fieldMappings[$index] = $attrData;
        }
        return $fieldMappings;
    }
}
