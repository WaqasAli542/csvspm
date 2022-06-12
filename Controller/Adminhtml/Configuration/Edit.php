<?php

namespace WMZ\CSVSPM\Controller\Adminhtml\Configuration;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Result\PageFactory;
use WMZ\CSVSPM\Api\CSVSPMConfigurationRepositoryInterface;
use WMZ\CSVSPM\Api\Data\CSVSPMConfigurationInterface;

class Edit extends Action
{
    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @var CSVSPMConfigurationRepositoryInterface
     */
    private $CSVSPMConfigurationRepository;

    /**
     * Edit constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param CSVSPMConfigurationRepositoryInterface $CSVSPMConfigurationRepository
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        CSVSPMConfigurationRepositoryInterface $CSVSPMConfigurationRepository
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->CSVSPMConfigurationRepository = $CSVSPMConfigurationRepository;
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('WMZ_CSVSPM::CSVSPM_Configuration');
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        /** @var Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $id = $this->getRequest()->getParam(CSVSPMConfigurationInterface::CSVSPM_ID);
        $configuration = null;
        if (isset($id)) {
            try {
                $configuration = $this->CSVSPMConfigurationRepository->getById($id);
            } catch (NoSuchEntityException $exception) {
                $this->messageManager->addErrorMessage($exception->getMessage());
                return $this->resultRedirectFactory->create()->setPath('*/*/');
            }
        }
        $resultPage->setActiveMenu('WMZ_CSVSPM::CSVSPM_Configuration');
        $resultPage->getConfig()->getTitle()
            ->prepend(
                isset($configuration) && $configuration->getCSVSPMId() ?
                __('Edit CSVSPM Configuration') : __('New CSVSPM Configuration')
            );
        return $resultPage;
    }
}
