<?php

namespace WMZ\CSVSPM\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Shipping\Model\Config;
use Magento\Framework\App\Config\ScopeConfigInterface;

class ShippingMethod implements OptionSourceInterface
{
    /**
     * @var Config
     */
    private $shippingMethodList;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfigInterface;

    /**
     * ShippingMethod constructor.
     * @param Config $shippingMethodList
     * @param ScopeConfigInterface $scopeConfigInterface
     */
    public function __construct(
        Config $shippingMethodList,
        ScopeConfigInterface $scopeConfigInterface
    ) {
        $this->shippingMethodList = $shippingMethodList;
        $this->scopeConfigInterface = $scopeConfigInterface;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $shippingMethods = $this->shippingMethodList->getAllCarriers();
        $result = [];
        foreach ($shippingMethods as $shippingCode => $shippingModel) {
            $carrierTitle = $this->scopeConfigInterface->getValue('carriers/' . $shippingCode . '/title');
            $result[] = [
                'label' => $carrierTitle,
                'value' => $shippingCode
            ];
        }
        return $result;
    }

    /**
     * @param $storeId
     * @return array
     */
    public function getShippingMethodList($storeId)
    {
        $shippingMethods = $this->shippingMethodList->getActiveCarriers($storeId);
        $result = [];
        foreach ($shippingMethods as $shippingCode => $shippingModel) {
            $carrierTitle = $this->scopeConfigInterface->getValue('carriers/' . $shippingCode . '/title');
            $result[] = [
                'label' => $carrierTitle,
                'value' => $shippingCode
            ];
        }
        return $result;
    }
}
