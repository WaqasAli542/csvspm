<?php

namespace WMZ\CSVSPM\Api\Data;

interface CSVSPMConfigurationInterface
{
    const CSVSPM_ID = 'csvspm_id';
    const STORE_VIEW = 'store_view';
    const CUSTOMER_GROUP = 'customer_group';
    const PAYMENT_METHODS = 'payment_methods';
    const STATUS = 'status';
    const SWAGGER_XML_PATH = 'csvspm/general/swagger_apis';
    const SHIPPING_METHOD = 'shipping_methods';

    /**
     * @return int
     */
    public function getCSVSPMId();

    /**
     * @param int $id
     * @return CSVSPMConfigurationInterface
     */
    public function setCSVSPMId($id);

    /**
     * @return int
     */
    public function getStoreView();

    /**
     * @param int $storeView
     * @return CSVSPMConfigurationInterface
     */
    public function setStoreView($storeView);

    /**
     * @return int
     */
    public function getCustomerGroup();

    /**
     * @param int $customerGroup
     * @return CSVSPMConfigurationInterface
     */
    public function setCustomerGroup($customerGroup);

    /**
     * @return string
     */
    public function getPaymentMethods();

    /**
     * @param string $paymentMethods
     * @return CSVSPMConfigurationInterface
     */
    public function setPaymentMethods($paymentMethods);

    /**
     * @return boolean
     */
    public function getStatus();

    /**
     * @param int $status
     * @return CSVSPMConfigurationInterface
     */
    public function setStatus($status);

    /**
     * @return string
     */
    public function getShippingMethods();

    /**
     * @param string $shippingMethods
     * @return CSVSPMConfigurationInterface
     */
    public function setShippingMethods($shippingMethods);
}
