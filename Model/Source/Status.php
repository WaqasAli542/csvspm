<?php

namespace WMZ\CSVSPM\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;

class Status implements OptionSourceInterface
{
    const ENABLE = 1;
    const DISABLE = 0;

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'label' => 'Enable',
                'value' => self::ENABLE
            ],
            [
                'label' => 'Disable',
                'value' => self::DISABLE
            ]
        ];
    }
}
