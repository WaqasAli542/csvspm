<?php

namespace WMZ\CSVSPM\Ui\Component\Listing\Column;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

class PaymentMethods extends Column
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * Payment constructor.
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
                $paymentMethodCodeArray = explode(", ", $item[$this->getData('name')]);
                $paymentTitle = "";
                foreach ($paymentMethodCodeArray as $code) {
                    $paymentTitle = $paymentTitle .
                        $this->scopeConfig->getValue('payment/' . $code . '/title') . ', ';
                }
                $item[$this->getData('name')] = rtrim($paymentTitle, ", ");
            }
        }
        return $dataSource;
    }
}
