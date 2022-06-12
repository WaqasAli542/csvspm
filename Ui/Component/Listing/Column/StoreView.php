<?php

namespace WMZ\CSVSPM\Ui\Component\Listing\Column;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Store\Api\StoreRepositoryInterface as StoreRepository;
use Magento\Ui\Component\Listing\Columns\Column;

class StoreView extends Column
{
    /**
     * @var StoreRepository
     */
    private $storeRepository;

    /**
     * StoreView constructor.
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param StoreRepository $storeRepository
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        StoreRepository $storeRepository,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->storeRepository = $storeRepository;
    }

    /**
     * @param array $dataSource
     * @return array
     * @throws NoSuchEntityException
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                try {
                    $storeView = $this->storeRepository->getById(
                        (int)$item[$this->getData('name')]
                    )->getName();
                } catch (NoSuchEntityException $exception) {
                    $storeView = '';
                }
                $item[$this->getData('name')] = $storeView;
            }
        }
        return $dataSource;
    }
}
