<?php

namespace WMZ\CSVSPM\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class CSVSPMConfig implements OptionSourceInterface
{
    const ENABLED = 1;
    const DISABLED = 0;

    /**
     * @inheritDoc
     */
    public function toOptionArray()
    {
        return [
            [
                'label' => 'Yes',
                'value' => self::ENABLED
            ],
            [
                'label' => 'No',
                'value' => self::DISABLED
            ]
        ];
    }
}
