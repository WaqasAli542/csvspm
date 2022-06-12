<?php

namespace WMZ\CSVSPM\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface CSVSPMConfigurationResultsInterface extends SearchResultsInterface
{
    /**
     * Get pages list.
     * @return CSVSPMConfigurationInterface[]
     */
    public function getItems();

    /**
     * Set pages list.
     * @param CSVSPMConfigurationInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
