<?php

namespace WMZ\CSVSPM\Model\ResourceModel\CSVSPMConfiguration;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use WMZ\CSVSPM\Api\Data\CSVSPMConfigurationInterface;
use WMZ\CSVSPM\Model\CSVSPMConfiguration;
use WMZ\CSVSPM\Model\ResourceModel\CSVSPMConfiguration as CSVSPMResource;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = CSVSPMConfigurationInterface::CSVSPM_ID;

    /**
     * @inheritDoc
     */
    public function _construct()
    {
        $this->_init(CSVSPMConfiguration::class, CSVSPMResource::class);
    }

    /**
     * @param $field
     * @param null $condition
     * @return mixed
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if ($field === CSVSPMConfigurationInterface::SHIPPING_METHOD) {
            $conditionSec = ['like' => '%' . $condition['eq'] . '%'];
            return parent::addFieldToFilter($field, $conditionSec);
        }
        return parent::addFieldToFilter($field, $condition);
    }
}
