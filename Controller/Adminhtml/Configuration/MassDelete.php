<?php

namespace WMZ\CSVSPM\Controller\Adminhtml\Configuration;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\LocalizedException;
use WMZ\CSVSPM\Api\CSVSPMConfigurationRepositoryInterface;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use WMZ\CSVSPM\Model\ResourceModel\CSVSPMConfiguration\CollectionFactory;
use Magento\Ui\Component\MassAction\Filter;

class MassDelete extends Action
{
    /**
     * @var CSVSPMConfigurationRepositoryInterface
     */
    private $configurationRepository;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var Filter
     */
    private $filter;

    /**
     * Delete constructor.
     * @param Context $context
     * @param CSVSPMConfigurationRepositoryInterface $configurationRepository
     * @param CollectionFactory $collectionFactory
     * @param Filter $filter
     */
    public function __construct(
        Context $context,
        CSVSPMConfigurationRepositoryInterface $configurationRepository,
        CollectionFactory $collectionFactory,
        Filter $filter
    ) {
        parent::__construct($context);
        $this->configurationRepository = $configurationRepository;
        $this->collectionFactory = $collectionFactory;
        $this->filter = $filter;
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
     * @throws CouldNotDeleteException
     * @throws LocalizedException
     */
    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $resultRedirect = $this->resultRedirectFactory->create();
        $size = $collection->getSize();
        foreach ($collection->getAllIds() as $id) {
            $this->configurationRepository->deleteById($id);
        }
        $this->messageManager->addSuccessMessage(
            __('A total of %1 record(s) have been deleted.', $size)
        );
        return $resultRedirect->setPath('*/*/');
    }
}
