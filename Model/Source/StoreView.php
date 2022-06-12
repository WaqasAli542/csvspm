<?php

namespace WMZ\CSVSPM\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Store\Ui\Component\Listing\Column\Store\Options as StoreOptions;

class StoreView extends StoreOptions implements OptionSourceInterface
{
    /**
     * @inheritDoc
     */
    public function toOptionArray()
    {
        $storeCollection = $this->systemStore->getStoreCollection();
        $result = [];
        foreach ($storeCollection as $store) {
            $result[] = [
                'label' => $store->getName(),
                'value' => $store->getStoreId()
            ];
        }
        return $result;
    }

    /**
     * @return array
     */
    public function getAllStoreView()
    {
        if ($this->options !== null) {
            return $this->options;
        }
        $this->generateCurrentOptions();

        $this->options = array_values($this->currentOptions);

        return $this->options;
    }
}
