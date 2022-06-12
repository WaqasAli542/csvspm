<?php

namespace WMZ\CSVSPM\Model;

use WMZ\CSVSPM\Api\Data\CSVSPMConfigurationInterface;
use Magento\Framework\Model\AbstractModel;

class CSVSPMConfiguration extends AbstractModel implements CSVSPMConfigurationInterface
{
    /**
     * @inheritDoc
     */
    public function _construct()
    {
        $this->_init(ResourceModel\CSVSPMConfiguration::class);
    }

    /**
     * @inheritDoc
     */
    public function getCSVSPMId()
    {
        return $this->getData(CSVSPMConfigurationInterface::CSVSPM_ID);
    }

    /**
     * @inheritDoc
     */
    public function setCSVSPMId($id)
    {
        return $this->setData(CSVSPMConfigurationInterface::CSVSPM_ID, $id);
    }

    /**
     * @inheritDoc
     */
    public function getStoreView()
    {
        return $this->getData(CSVSPMConfigurationInterface::STORE_VIEW);
    }

    /**
     * @inheritDoc
     */
    public function setStoreView($storeView)
    {
        return $this->setData(CSVSPMConfigurationInterface::STORE_VIEW, $storeView);
    }

    /**
     * @inheritDoc
     */
    public function getCustomerGroup()
    {
        return $this->getData(CSVSPMConfigurationInterface::CUSTOMER_GROUP);
    }

    /**
     * @inheritDoc
     */
    public function setCustomerGroup($customerGroup)
    {
        return $this->setData(CSVSPMConfigurationInterface::CUSTOMER_GROUP, $customerGroup);
    }

    /**
     * @inheritDoc
     */
    public function getPaymentMethods()
    {
        return $this->getData(CSVSPMConfigurationInterface::PAYMENT_METHODS);
    }

    /**
     * @inheritDoc
     */
    public function setPaymentMethods($paymentMethods)
    {
        return $this->setData(CSVSPMConfigurationInterface::PAYMENT_METHODS, $paymentMethods);
    }

    /**
     * @inheritDoc
     */
    public function getStatus()
    {
        return $this->getData(CSVSPMConfigurationInterface::STATUS);
    }

    /**
     * @inheritDoc
     */
    public function setStatus($status)
    {
        return $this->setData(CSVSPMConfigurationInterface::STATUS, $status);
    }

    /**
     * @inheritDoc
     */
    public function getShippingMethods()
    {
        return $this->getData(CSVSPMConfigurationInterface::SHIPPING_METHOD);
    }

    /**
     * @inheritDoc
     */
    public function setShippingMethods($shippingMethods)
    {
        return $this->setData(CSVSPMConfigurationInterface::SHIPPING_METHOD, $shippingMethods);
    }
}
