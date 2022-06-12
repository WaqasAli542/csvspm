<?php

namespace WMZ\CSVSPM\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use WMZ\CSVSPM\Api\Data\CSVSPMConfigurationInterface;

class CSVSPMConfiguration extends AbstractDb
{
    const TABLE_NAME = 'wmz_csvspm';

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, CSVSPMConfigurationInterface::CSVSPM_ID);
    }
}
