<?php

namespace WMZ\CSVSPM\Ui\Component\Listing\Filter;

use Magento\Framework\Api\Filter;
use Magento\Framework\Data\Collection;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\UiComponent\DataProvider\FilterApplierInterface;

class CustomFilter implements FilterApplierInterface
{
    /**
     * Apply regular filters like collection filters
     * @param Collection $collection
     * @param Filter $filter
     * @return void
     * @throws LocalizedException
     */
    public function apply(Collection $collection, Filter $filter)
    {
        $collection->addFieldToFilter($filter->getField(), ['like' => '%' . $filter->getValue() . '%']);
    }
}
