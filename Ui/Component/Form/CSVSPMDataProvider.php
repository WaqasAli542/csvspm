<?php

namespace WMZ\CSVSPM\Ui\Component\Form;

use Magento\Ui\DataProvider\AbstractDataProvider;
use Magento\Ui\DataProvider\Modifier\PoolInterface;
use WMZ\CSVSPM\Api\Data\CSVSPMConfigurationInterface;
use WMZ\CSVSPM\Model\ResourceModel\CSVSPMConfiguration\CollectionFactory;

class CSVSPMDataProvider extends AbstractDataProvider
{
    /**
     * @var PoolInterface
     */
    private $pool;

    /**
     * CSVSPMDataProvider constructor.
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param PoolInterface $pool
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        PoolInterface $pool,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $meta,
            $data
        );
        $this->collection = $collectionFactory->create();
        $this->pool = $pool;
    }

    /**
     * @return array
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }

        $items = $this->collection->getItems();
        $this->loadedData = [];
        /** @var CSVSPMConfigurationInterface $item */
        foreach ($items as $item) {
            $this->loadedData[$item->getCSVSPMId()] = $item->getData();
            $this->loadedData[$item->getCSVSPMId()][CSVSPMConfigurationInterface::PAYMENT_METHODS] =
                explode(', ', $item->getPaymentMethods());
            $this->loadedData[$item->getCSVSPMId()][CSVSPMConfigurationInterface::SHIPPING_METHOD] =
                explode(', ', $item->getShippingMethods());
        }
        return $this->loadedData;
    }

    public function getMeta()
    {
        $meta = parent::getMeta();

        foreach ($this->pool->getModifiersInstances() as $modifier) {
            $meta = $modifier->modifyMeta($meta);
        }
        return $meta;
    }
}
