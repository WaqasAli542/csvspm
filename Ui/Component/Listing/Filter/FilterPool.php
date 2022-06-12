<?php

namespace WMZ\CSVSPM\Ui\Component\Listing\Filter;

use Magento\Framework\Data\Collection;
use Magento\Framework\Api\Search\SearchCriteriaInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\FilterApplierInterface;
use WMZ\CSVSPM\Api\Data\CSVSPMConfigurationInterface;

class FilterPool extends \Magento\Framework\View\Element\UiComponent\DataProvider\FilterPool
{
    /**
     * @param Collection $collection
     * @param SearchCriteriaInterface $criteria
     * @return void
     */
    public function applyFilters(Collection $collection, SearchCriteriaInterface $criteria)
    {
        foreach ($criteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                /** @var $filterApplier FilterApplierInterface */
                if (isset($this->appliers[$filter->getConditionType()])) {
                    $filterApplier = $this->appliers[$filter->getConditionType()];
                } elseif ($filter->getField() == CSVSPMConfigurationInterface::SHIPPING_METHOD ||
                    $filter->getField() == CSVSPMConfigurationInterface::PAYMENT_METHODS
                ) {
                    $filterApplier = $this->appliers['customFilter'];
                } else {
                    $filterApplier = $this->appliers['regular'];
                }
                $filterApplier->apply($collection, $filter);
            }
        }
    }
}
