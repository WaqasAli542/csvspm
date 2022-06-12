<?php

namespace WMZ\CSVSPM\Model;

use Magento\Framework\App\ObjectManager;
use WMZ\CSVSPM\Api\Data\CSVSPMConfigurationInterface;
use WMZ\CSVSPM\Api\Data\CSVSPMConfigurationResultsInterface;
use WMZ\CSVSPM\Api\Data\CSVSPMConfigurationResultsInterfaceFactory;
use WMZ\CSVSPM\Api\Data\CSVSPMConfigurationInterfaceFactory;
use WMZ\CSVSPM\Api\CSVSPMConfigurationRepositoryInterface;
use WMZ\CSVSPM\Model\Api\SearchCriteria\ConfigurationCollectionProcessor;
use WMZ\CSVSPM\Model\ResourceModel\CSVSPMConfiguration as CSVSPMConfigurationResource;
use WMZ\CSVSPM\Model\ResourceModel\CSVSPMConfiguration\CollectionFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class CSVSPMConfigurationRepository implements CSVSPMConfigurationRepositoryInterface
{
    /**
     * @var array
     */
    private $CSVSPMConfigurations;

    /**
     * @var CSVSPMConfigurationResource
     */
    private $CSVSPMConfigurationResource;

    /**
     * @var CSVSPMConfigurationInterfaceFactory
     */
    private $CSVSPMConfigurationFactory;

    /**
     * @var CollectionFactory
     */
    private $CSVSPMCollectionFactory;

    /**
     * @var mixed
     */
    private $collectionProcessor;

    /**
     * @var CSVSPMConfigurationResultsInterfaceFactory
     */
    private $configurationResultsFactory;

    /**
     * CSVSPMConfigurationRepository constructor.
     * @param CSVSPMConfigurationResource $CSVSPMConfigurationResource
     * @param CSVSPMConfigurationInterfaceFactory $CSVSPMConfigurationFactory
     * @param CollectionFactory $CSVSPMCollectionFactory
     * @param CSVSPMConfigurationResultsInterfaceFactory $configurationResultsFactory
     * @param CollectionProcessorInterface|null $collectionProcessor
     */
    public function __construct(
        CSVSPMConfigurationResource $CSVSPMConfigurationResource,
        CSVSPMConfigurationInterfaceFactory $CSVSPMConfigurationFactory,
        CollectionFactory $CSVSPMCollectionFactory,
        CSVSPMConfigurationResultsInterfaceFactory $configurationResultsFactory,
        CollectionProcessorInterface $collectionProcessor = null
    ) {
        $this->CSVSPMConfigurationResource = $CSVSPMConfigurationResource;
        $this->CSVSPMConfigurationFactory = $CSVSPMConfigurationFactory;
        $this->CSVSPMCollectionFactory = $CSVSPMCollectionFactory;
        $this->configurationResultsFactory = $configurationResultsFactory;
        $this->collectionProcessor = $collectionProcessor ?: $this->getCollectionProcessor();
    }

    /**
     * @inheritDoc
     */
    public function save(CSVSPMConfigurationInterface $CSVSPMConfiguration)
    {
        try {
            if ($CSVSPMConfiguration->getCSVSPMId()) {
                $CSVSPMConfiguration = $this->getById($CSVSPMConfiguration->getCSVSPMId())
                    ->addData($CSVSPMConfiguration->getData());
            }
            $this->CSVSPMConfigurationResource->save($CSVSPMConfiguration);
            unset($this->CSVSPMConfigurations[$CSVSPMConfiguration->getCSVSPMId()]);
        } catch (\Exception $e) {
            if ($CSVSPMConfiguration->getCSVSPMId()) {
                throw new CouldNotSaveException(
                    __(
                        'Unable to save configuration with ID %1. Error: %2',
                        [$CSVSPMConfiguration->getCSVSPMId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotSaveException(__('Unable to save new configuration. Error: %1', $e->getMessage()));
        }

        return $CSVSPMConfiguration;
    }

    /**
     * @inheritDoc
     */
    public function getById($id)
    {
        if (!isset($this->CSVSPMConfigurations[$id])) {
            $CSVSPMConfiguration = $this->CSVSPMConfigurationFactory->create();
            $this->CSVSPMConfigurationResource->load($CSVSPMConfiguration, $id);
            if (!$CSVSPMConfiguration->getId()) {
                throw new NoSuchEntityException(__('Configuration with specified ID "%1" not found.', $id));
            }
            $this->CSVSPMConfigurations[$id] = $CSVSPMConfiguration;
        }

        return $this->CSVSPMConfigurations[$id];
    }

    /**
     * @inheritDoc
     */
    public function delete(CSVSPMConfigurationInterface $CSVSPMConfiguration)
    {
        try {
            $this->CSVSPMConfigurationResource->delete($CSVSPMConfiguration);
            unset($this->CSVSPMConfigurations[$CSVSPMConfiguration->getCSVSPMId()]);
        } catch (\Exception $e) {
            if ($CSVSPMConfiguration->getCSVSPMId()) {
                throw new CouldNotDeleteException(
                    __(
                        'Unable to remove configuration with ID %1. Error: %2',
                        [$CSVSPMConfiguration->getCSVSPMId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotDeleteException(__('Unable to remove configuration. Error: %1', $e->getMessage()));
        }
        return true;
    }

    /**
     * @inheritDoc
     * @throws NoSuchEntityException
     */
    public function deleteById($id)
    {
        $CSVSPMConfiguration = $this->getById($id);
        $this->delete($CSVSPMConfiguration);
        return true;
    }

    /**
     * @inheritDoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $collection = $this->CSVSPMCollectionFactory->create();
        $this->collectionProcessor->process($searchCriteria, $collection);
        /** @var CSVSPMConfigurationResultsInterface $searchResults */
        $searchResults = $this->configurationResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * @return mixed
     */
    private function getCollectionProcessor()
    {
        if (!$this->collectionProcessor) {
            $this->collectionProcessor = ObjectManager::getInstance()->get(
                ConfigurationCollectionProcessor::class
            );
        }
        return $this->collectionProcessor;
    }
}
