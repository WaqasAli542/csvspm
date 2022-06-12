<?php

namespace WMZ\CSVSPM\Observer;

use Magento\Customer\Model\Group;
use Magento\Customer\Model\SessionFactory;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\HTTP\Header;
use Magento\Payment\Model\MethodInterface;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use WMZ\CSVSPM\Api\CSVSPMConfigurationRepositoryInterface;
use WMZ\CSVSPM\Api\Data\CSVSPMConfigurationInterface;
use WMZ\CSVSPM\Model\Source\Status;

class PaymentActive implements ObserverInterface
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var Header
     */
    private $httpHeader;

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
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    const CSVSPM_CONFIG_PATH = 'csvspm/csvspmGroup/csvspmField';

    /**
     * PaymentActive constructor.
     * @param StoreManagerInterface $storeManager
     * @param Header $httpHeader
     * @param SessionFactory $customerSessionFactory
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param CSVSPMConfigurationRepositoryInterface $configurationRepository
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        Header $httpHeader,
        SessionFactory $customerSessionFactory,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        CSVSPMConfigurationRepositoryInterface $configurationRepository,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->storeManager = $storeManager;
        $this->httpHeader = $httpHeader;
        $this->customerSessionFactory = $customerSessionFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->configurationRepository = $configurationRepository;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @inheritDoc
     */
    public function execute(Observer $observer)
    {
        /** @var MethodInterface $method */
        $method = $observer->getEvent()->getMethodInstance();
        $result = $observer->getEvent()->getResult();
        /** @var CartInterface $quote */
        $quote = $observer->getEvent()->getQuote();
        $scopeConfigValue = $this->scopeConfig->getValue(self::CSVSPM_CONFIG_PATH, ScopeInterface::SCOPE_STORE);
        if ($quote) {
            $storeId = $quote->getStoreId();
            $groupId = $quote->getCustomer()->getGroupId();
        } else {
            $storeId = $this->storeManager->getStore()->getId();
            $customerSession = $this->customerSessionFactory->create();
            if ($customerSession->isLoggedIn()) {
                $groupId = $customerSession->getCustomer()->getGroupId();
            } else {
                $groupId = Group::NOT_LOGGED_IN_ID;
            }
        }

        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(CSVSPMConfigurationInterface::STORE_VIEW, $storeId)
            ->addFilter(CSVSPMConfigurationInterface::STATUS, Status::ENABLE)
            ->addFilter(CSVSPMConfigurationInterface::CUSTOMER_GROUP, $groupId)
            ->create();
        try {
            $searchResult = $this->configurationRepository->getList($searchCriteria);
            $configurationList = $searchResult->getItems();
            $isAvailable = false;
            /** @var CSVSPMConfigurationInterface $configuration */
            if ($searchResult->getTotalCount() > 0) {
                foreach ($configurationList as $configuration) {
                    $paymentMethodsString = $configuration->getPaymentMethods();
                    if (isset($paymentMethodsString) && !empty($paymentMethodsString)) {
                        $paymentMethods = explode(', ', $paymentMethodsString);
                        foreach ($paymentMethods as $paymentMethod) {
                            if ($paymentMethod == $method->getCode()) {
                                $isAvailable = true;
                                break;
                            }
                        }
                    }
                }
            } else {
                $isAvailable = $scopeConfigValue;
            }
        } catch (NoSuchEntityException $e) {
            $isAvailable = false;
        }
        $result->setData('is_available', $isAvailable);
    }
}
