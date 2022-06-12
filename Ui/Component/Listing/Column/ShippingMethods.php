<?php

namespace WMZ\CSVSPM\Ui\Component\Listing\Column;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

class ShippingMethods extends Column
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * Shipping constructor
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param ScopeConfigInterface $scopeConfig
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        ScopeConfigInterface $scopeConfig,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $shippingMethodName = explode(", ", $item[$this->getData('name')]);
                $shippingMethod = "";
                foreach ($shippingMethodName as $code) {
                    $shippingMethod = $shippingMethod .
                        $this->scopeConfig->getValue('carriers/' . $code . '/title') . ', ';
                }
                $item[$this->getData('name')] = rtrim($shippingMethod, ", ");
            }
        }
        return $dataSource;
    }
}
