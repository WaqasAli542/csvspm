<?php

namespace WMZ\CSVSPM\Model;

use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\Customer\Model\Group;
use Magento\Customer\Model\SessionFactory;
use Magento\Directory\Model\RegionFactory;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Math\Division;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Quote\Model\Quote\Address\RateRequestFactory;
use Magento\Shipping\Model\CarrierFactory;
use Magento\Shipping\Model\Config;
use Magento\Shipping\Model\Rate\CarrierResultFactory;
use Magento\Shipping\Model\Rate\PackageResultFactory;
use Magento\Shipping\Model\Rate\ResultFactory;
use Magento\Shipping\Model\Shipment\RequestFactory;
use Magento\Shipping\Model\Shipping as MagentoShipping;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use WMZ\CSVSPM\Api\Data\CSVSPMConfigurationInterface;
use WMZ\CSVSPM\Api\CSVSPMConfigurationRepositoryInterface;
use WMZ\CSVSPM\Model\Source\Status;

class Shipping extends MagentoShipping
{
    /** CSVSPM configuration path */
    const CSVSPM_CONFIG_PATH = 'csvspm/csvspmGroup/csvspmField';

    /**
     * @var SessionFactory
     */
    private $customerSessionFactory;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var CSVSPMConfigurationRepositoryInterface
     */
    private $configurationRepository;

    /**
     * Shipping constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param Config $shippingConfig
     * @param StoreManagerInterface $storeManager
     * @param CarrierFactory $carrierFactory
     * @param ResultFactory $rateResultFactory
     * @param RequestFactory $shipmentRequestFactory
     * @param RegionFactory $regionFactory
     * @param Division $mathDivision
     * @param StockRegistryInterface $stockRegistry
     * @param SessionFactory $customerSessionFactory
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param CSVSPMConfigurationRepositoryInterface $configurationRepository
     * @param RateRequestFactory|null $rateRequestFactory
     * @param PackageResultFactory|null $packageResultFactory
     * @param CarrierResultFactory|null $carrierResultFactory
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Config $shippingConfig,
        StoreManagerInterface $storeManager,
        CarrierFactory $carrierFactory,
        ResultFactory $rateResultFactory,
        RequestFactory $shipmentRequestFactory,
        RegionFactory $regionFactory,
        Division $mathDivision,
        StockRegistryInterface $stockRegistry,
        SessionFactory $customerSessionFactory,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        CSVSPMConfigurationRepositoryInterface $configurationRepository,
        RateRequestFactory $rateRequestFactory = null,
        PackageResultFactory $packageResultFactory = null,
        CarrierResultFactory $carrierResultFactory = null
    ) {
        parent::__construct(
            $scopeConfig,
            $shippingConfig,
            $storeManager,
            $carrierFactory,
            $rateResultFactory,
            $shipmentRequestFactory,
            $regionFactory,
            $mathDivision,
            $stockRegistry,
            $rateRequestFactory,
            $packageResultFactory,
            $carrierResultFactory
        );
        $this->customerSessionFactory = $customerSessionFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->configurationRepository = $configurationRepository;
    }

    /**
     * @param string $carrierCode
     * @param RateRequest $request
     * @return $this|MagentoShipping
     */
    public function collectCarrierRates($carrierCode, $request)
    {
        if (!$this->checkCarrierAvailability($carrierCode)) {
            return $this;
        }
        return parent::collectCarrierRates($carrierCode, $request);
    }

    /**
     * @param $carrierCode
     * @return bool
     */
    private function checkCarrierAvailability($carrierCode)
    {
        $flag = false;
        $customerSession = $this->customerSessionFactory->create();
        if ($customerSession->isLoggedIn()) {
            $groupId = $customerSession->getCustomer()->getGroupId();
        } else {
            $groupId = Group::NOT_LOGGED_IN_ID;
        }
        try {
            $storeId = $this->_storeManager->getStore()->getId();
            $scopeConfigValue = $this->_scopeConfig->getValue(self::CSVSPM_CONFIG_PATH, ScopeInterface::SCOPE_STORE);
            $searchCriteria = $this->searchCriteriaBuilder
                ->addFilter(CSVSPMConfigurationInterface::STORE_VIEW, $storeId)
                ->addFilter(CSVSPMConfigurationInterface::STATUS, Status::ENABLE)
                ->addFilter(CSVSPMConfigurationInterface::CUSTOMER_GROUP, $groupId)
                ->create();
            $searchResult = $this->configurationRepository->getList($searchCriteria);
            $configurationList = $searchResult->getItems();
            /** @var CSVSPMConfigurationInterface $configuration */
            if ($searchResult->getTotalCount() > 0) {
                foreach ($configurationList as $configuration) {
                    $shippingMethodsString = $configuration->getShippingMethods();
                    if (isset($shippingMethodsString) && !empty($shippingMethodsString)) {
                        $shippingMethods = explode(', ', $shippingMethodsString);
                        foreach ($shippingMethods as $shippingMethod) {
                            if ($shippingMethod == $carrierCode) {
                                $flag = true;
                                break;
                            }
                        }
                    }
                }
            } else {
                $flag = $scopeConfigValue;
            }

        } catch (NoSuchEntityException $exception) {
            $flag = false;
        }
        return $flag;
    }
}
