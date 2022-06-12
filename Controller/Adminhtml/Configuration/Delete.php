<?php

namespace WMZ\CSVSPM\Controller\Adminhtml\Configuration;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use WMZ\CSVSPM\Api\Data\CSVSPMConfigurationInterface;
use WMZ\CSVSPM\Api\CSVSPMConfigurationRepositoryInterface;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\CouldNotDeleteException;

class Delete extends Action
{
    /**
     * @var CSVSPMConfigurationRepositoryInterface
     */
    private $configurationRepository;

    /**
     * Delete constructor.
     * @param Context $context
     * @param CSVSPMConfigurationRepositoryInterface $configurationRepository
     */
    public function __construct(
        Context $context,
        CSVSPMConfigurationRepositoryInterface $configurationRepository
    ) {
        parent::__construct($context);
        $this->configurationRepository = $configurationRepository;
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
        $id = $this->getRequest()->getParam(CSVSPMConfigurationInterface::CSVSPM_ID);
        $resultRedirect = $this->resultRedirectFactory->create();
        if (isset($id)) {
            try {
                $this->configurationRepository->deleteById($id);
                $this->messageManager->addSuccessMessage(__('The Record has been deleted.'));
            } catch (CouldNotDeleteException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        } else {
            $this->messageManager->addErrorMessage(__('We can\'t find a record to delete.'));
        }
        return $resultRedirect->setPath('*/*/');
    }
}
