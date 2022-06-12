<?php

namespace WMZ\CSVSPM\Model\Source;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Data\OptionSourceInterface;
use Magento\Payment\Model\Config;
use Magento\Payment\Model\PaymentMethodList;

class PaymentMethod implements OptionSourceInterface
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var Config
     */
    private $paymentConfig;

    /**
     * @var PaymentMethodList
     */
    private $paymentMethodList;

    /**
     * Payment constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param Config $paymentConfig
     * @param PaymentMethodList $paymentMethodList
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Config $paymentConfig,
        PaymentMethodList $paymentMethodList
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->paymentConfig = $paymentConfig;
        $this->paymentMethodList = $paymentMethodList;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $payments = $this->paymentConfig->getActiveMethods();
        $methods = [];
        foreach ($payments as $paymentCode => $paymentModel) {
            $paymentTitle = $this->scopeConfig->getValue('payment/' . $paymentCode . '/title');
            $methods[] = [
                'label' => $paymentTitle,
                'value' => $paymentCode
            ];
        }
        return $methods;
    }

    /**
     * @param $storeId
     * @return array
     */
    public function getPaymentMethodList($storeId)
    {
        $paymentMethodList = $this->paymentMethodList->getActiveList($storeId);
        $result = [];
        foreach ($paymentMethodList as $paymentMethod) {
            $result[] = [
                'label' => $paymentMethod->getTitle(),
                'value' => $paymentMethod->getCode()
            ];
        }
        return $result;
    }
}
