<?php

namespace WMZ\CSVSPM\Controller\Adminhtml\Configuration;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use WMZ\CSVSPM\Api\Data\CSVSPMConfigurationInterface;
use WMZ\CSVSPM\Api\Data\CSVSPMConfigurationInterfaceFactory;
use WMZ\CSVSPM\Api\CSVSPMConfigurationRepositoryInterface;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\CouldNotSaveException;

class Save extends Action
{
    /**
     * @var CSVSPMConfigurationRepositoryInterface
     */
    private $configurationRepository;

    /**
     * @var CSVSPMConfigurationInterfaceFactory
     */
    private $configurationInterfaceFactory;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * Save constructor.
     * @param Context $context
     * @param CSVSPMConfigurationRepositoryInterface $configurationRepository
     * @param CSVSPMConfigurationInterfaceFactory $configurationInterfaceFactory
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        Context $context,
        CSVSPMConfigurationRepositoryInterface $configurationRepository,
        CSVSPMConfigurationInterfaceFactory $configurationInterfaceFactory,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        parent::__construct($context);
        $this->configurationRepository = $configurationRepository;
        $this->configurationInterfaceFactory = $configurationInterfaceFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('WMZ_CSVSPM::CSVSPM_Configuration');
    }

    /**
     * @return ResponseInterface|Redirect|ResultInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $postData = $this->getRequest()->getPostValue();
        if ($postData) {
            try {
                if (!isset($postData[CSVSPMConfigurationInterface::CSVSPM_ID])) {
                    $searchCriteria = $this->searchCriteriaBuilder
                        ->addFilter(
                            CSVSPMConfigurationInterface::STORE_VIEW,
                            $postData[CSVSPMConfigurationInterface::STORE_VIEW]
                        )
                        ->addFilter(CSVSPMConfigurationInterface::STATUS, 1)
                        ->addFilter(
                            CSVSPMConfigurationInterface::CUSTOMER_GROUP,
                            $postData[CSVSPMConfigurationInterface::CUSTOMER_GROUP]
                        )
                        ->create();
                    $searchResult = $this->configurationRepository->getList($searchCriteria);
                    if ($searchResult->getTotalCount() > 0) {
                        /** @var CSVSPMConfigurationInterface $configuration */
                        $configurationList = $searchResult->getItems();
                        $this->messageManager->addNoticeMessage(
                            __('There is already a configuration exist for
                             this particular Store View and Customer Group')
                        );
                        return $resultRedirect->setPath('*/*/edit', [
                            CSVSPMConfigurationInterface::CSVSPM_ID => reset($configurationList)->getCSVSPMId()
                        ]);
                    }
                }
                $configuration = $this->configurationInterfaceFactory->create();
                if (isset($postData[CSVSPMConfigurationInterface::CSVSPM_ID])) {
                    $configuration->setCSVSPMId($postData[CSVSPMConfigurationInterface::CSVSPM_ID]);
                } else {
                    $configuration->setStoreView($postData[CSVSPMConfigurationInterface::STORE_VIEW]);
                }
                $configuration->setStatus($postData[CSVSPMConfigurationInterface::STATUS]);
                $configuration->setCustomerGroup($postData[CSVSPMConfigurationInterface::CUSTOMER_GROUP]);
                if (isset($postData[CSVSPMConfigurationInterface::PAYMENT_METHODS]) &&
                    isset($postData[CSVSPMConfigurationInterface::SHIPPING_METHOD])) {
                    $paymentMethods = '';
                    if (is_array($postData[CSVSPMConfigurationInterface::PAYMENT_METHODS]) &&
                        count($postData[CSVSPMConfigurationInterface::PAYMENT_METHODS])) {
                        foreach ($postData[CSVSPMConfigurationInterface::PAYMENT_METHODS] as $paymentMethod) {
                            $paymentMethods .= $paymentMethod . ', ';
                        }
                    }
                    $configuration->setPaymentMethods(trim($paymentMethods, ', '));
                    $shippingMethods = '';
                    if (is_array($postData[CSVSPMConfigurationInterface::SHIPPING_METHOD]) &&
                        count($postData[CSVSPMConfigurationInterface::SHIPPING_METHOD])) {
                        foreach ($postData[CSVSPMConfigurationInterface::SHIPPING_METHOD] as $shippingMethod) {
                            $shippingMethods .= $shippingMethod . ', ';
                        }
                    }
                    $configuration->setShippingMethods(trim($shippingMethods, ', '));
                }
                $configuration = $this->configurationRepository->save($configuration);
                $this->messageManager->addSuccessMessage(__('CSVSPM Configuration has been saved'));
                return $resultRedirect->setPath('*/*/edit', [
                    CSVSPMConfigurationInterface::CSVSPM_ID => $configuration->getCSVSPMId()
                ]);
            } catch (CouldNotSaveException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the record.'));
            }
        }

        $this->messageManager->addErrorMessage(
            __('Something went wrong while saving the record. Please Try Again')
        );

        return $resultRedirect->setPath('*/*/');
    }
}
